<?php

namespace App\Http\Controllers;

use App\Models\RibiClub;
use App\Models\RibiCyeo;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CyeoController extends Controller
{
    public function cyeoJson(Request $request)
    {
        $user = auth()->user();
        $role = $user->roles[0]->name;
        $district = $user->district;

        $orderBy = $request->get('sortBy');
        $orderBy = $orderBy? $orderBy[0]: 'id';
        $sortDir = $request->get('sortDesc');
        $sortDir = ($sortDir && $sortDir[0]=='true')? 'desc': 'asc';
        $searchText = $request->get('searchText');

        $perPage = $request->get('itemsPerPage');
        if($role==='dyeo'){
            $cyeos = RibiCyeo::whereHas('club', function(Builder $query) use($district){
                $query->where('district_code', $district);
            })
            ->where('cyeo_name', 'like', "%$searchText%")
            ->with('club')
            ->paginate($perPage>0? $perPage: 3000);
        }
        else{
            $cyeos = RibiCyeo::where('cyeo_name', 'like', "%$searchText%")
            ->orWhereHas('club', function (Builder $query) use($searchText) {
                $query->where('club_name', 'like', "%$searchText%");
                $query->orWhere('district_code', 'like', "%$searchText%");
            })
            ->orderBy($orderBy, $sortDir)
            ->with('club')
            ->paginate($perPage>0? $perPage: 3000);
        }
        
        return response()->json($cyeos);
    }
    public function index(){
        return view('cyeos');
    }

    public function cyeoJsonData(){
        $ribi_clubs = RibiClub::orderBy('club_name')->get()->groupBy('district_code')->toArray();
        $districts = DB::table('districts')->get('code')->toArray();
        $districts= array_column($districts, 'code');
        return response()->json([
            'districts'=>$districts,
            'ribi_clubs'=>$ribi_clubs
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $cyeo = RibiCyeo::create($data);
        $cyeo->load('club');
        //Todo Create Corresponding User in Users Table for Login
        $user = User::create([
            'full_name'=>$cyeo->cyeo_name,
            'email'=>$cyeo->cyeo_email,
            'active'=>true,
            'password'=>Hash::make('12345678'),
            'rotary_club_id'=>$cyeo->ribi_club_id,
        ]);
        $user->assignRole('cyeo');
        $cyeo->user_id = $user->id;
        $cyeo->save();

        return response()->json(['message'=>'Club Youth Exchange Officer Added Successfully', 'cyeo'=>$cyeo]);
    }

    public function update($id, Request $request)
    {
        $data = $request->all();
        RibiCyeo::findOrfail($id)->update($data);
        $cyeo = RibiCyeo::find($id)->load('club');
        return response()->json(['message'=>'Club Youth Exchange Officer Updated Successfully', 'cyeo'=>$cyeo]);
    }

    public function destroy($id)
    {
        try {
            $cyeo = RibiCyeo::findOrfail($id);
            //Delete Corresponding User in Users Table for this Cyeo
            if($cyeo->user_id){
                $user = User::find($cyeo->user_id);
                if($user){
                    $user->delete();
                }
            }
            //Now Delete Cyeo Itself
            $cyeo->delete();
            return response()->json(['message'=>'Club Youth Exchange Officer Deleted Successfully']);
        }catch (\Exception $exception){}
    }
}
