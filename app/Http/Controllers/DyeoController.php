<?php

namespace App\Http\Controllers;

use App\Models\RibiClub;
use App\Models\RibiDyeo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DyeoController extends Controller
{
    public function dyeoJson(Request $request)
    {
        $orderBy = $request->get('sortBy');
        $orderBy = $orderBy? $orderBy[0]: 'id';
        $sortDir = $request->get('sortDesc');
        $sortDir = ($sortDir && $sortDir[0]=='true')? 'desc': 'asc';
        $searchText = $request->get('searchText');

        $perPage = $request->get('itemsPerPage');
        $dyeos = RibiDyeo::where('dyeo_name', 'like', "%$searchText%")
            ->orWhere('district_code', 'like', "%$searchText%")
            ->orWhere('dyeo_city', 'like', "%$searchText%")
            ->orderBy($orderBy, $sortDir)
            ->paginate($perPage>0? $perPage: 3000);
        return response()->json($dyeos);
    }
    public function index(){
        return view('dyeos');
    }

    public function dyeoJsonData(){
        $ribi_clubs = RibiClub::orderBy('club_name')->get()->toArray();
        $districts = DB::table('districts')->get(['id','code'])->toArray();
        $districts= array_column($districts, 'code');
        return response()->json([
            'districts'=>$districts,
            'ribi_clubs'=>$ribi_clubs
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $dyeo = RibiDyeo::create($data);
        //Todo Create Corresponding User in Users Table for Login
        $user = User::create([
            'full_name'=>$dyeo->dyeo_name,
            'email'=>$dyeo->dyeo_email,
            'active'=>true,
            'password'=>Hash::make('12345678'),
            'district'=>$dyeo->district_code,
        ]);
        $user->assignRole('dyeo');
        $dyeo->user_id = $user->id;
        $dyeo->save();
        return response()->json(['message'=>'District Youth Exchange Officer Added Successfully', 'dyeo'=>$dyeo]);
    }

    public function update($id, Request $request)
    {
        $data = $request->all();
        RibiDyeo::findOrfail($id)->update($data);
        $dyeo = Ribidyeo::find($id);
        return response()->json(['message'=>'District Youth Exchange Officer Updated Successfully', 'dyeo'=>$dyeo]);
    }

    public function destroy($id)
    {
        try {
            $dyeo = RibiDyeo::findOrfail($id);
            //Delete Corresponding User in Users Table for this Dyeo
            if($dyeo->user_id){
                $user = User::find($dyeo->user_id);
                if($user){
                    $user->delete();
                }
            }
            //Now Delete Dyeo Itself
            $dyeo->delete();
            return response()->json(['message'=>'District Youth Exchange Officer Deleted Successfully']);
        }catch (\Exception $exception){}
    }
}
