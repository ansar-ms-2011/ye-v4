<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\RibiClub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubController extends Controller
{
    public function clubsJson(Request $request)
    {
        $user = auth()->user();
        $role = $user->roles[0]->name;
        $orderBy = $request->get('sortBy');
        $orderBy = $orderBy ? $orderBy[0] : 'id';
        $sortDir = $request->get('sortDesc');
        $sortDir = ($sortDir && $sortDir[0] == 'true') ? 'desc' : 'asc';
        $searchText = $request->get('searchText');

        $perPage = $request->get('itemsPerPage');
        //If dyeo is looking clubs then only show club associated with dyeo district
        if ($role === 'dyeo') {
            $clubs = RibiClub::where('district_code', '=', $user->district)->where(function ($query) use ($searchText) {
                $query->orWhere('club_name', 'like', "%$searchText%");
            })->orderBy($orderBy, $sortDir)->paginate($perPage > 0 ? $perPage : 3000);
        } else {
            $clubs = RibiClub::where(function ($query) use ($searchText) {
                $query->where('club_name', 'like', "%$searchText%");
                $query->orWhere('district_code', 'like', "%$searchText%");
                $query->orWhere('club_president', 'like', "%$searchText%");
                $query->orWhere('club_president_email', 'like', "%$searchText%");
                $query->orWhere('club_president_mobile', 'like', "%$searchText%");
            })->orderBy($orderBy, $sortDir)->paginate($perPage > 0 ? $perPage : 3000);
        }

        return response()->json($clubs);
    }

    public function index()
    {
        $districts = DB::table('districts')->get('code')->toArray();
        $districts = array_column($districts, 'code');

        return view('clubs')->with(['districts' => $districts]);
    }

    /**
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['club_id'] = '12345';
        $dist = District::where('code', $data['district_code'])->first();
        if ($dist) {
            $data['district_id'] = $dist->id;
            $club = RibiClub::create($data);
            return response()->json(['message' => 'Club Added successfully', 'clubs' => $club]);
        } else {
            throw new \Exception('District Code Selected not found in Database');
        }
    }

    public function update($id, Request $request)
    {
        $data = $request->all();
        $club = RibiClub::findOrfail($id)->update($data);
        return response()->json(['message' => 'Club Updated successfully', 'clubs' => $club]);
    }

    public function destroy($club)
    {
        try {
            $club = RibiClub::findOrfail($club)->delete();
            return response()->json(['message' => 'Club Deleted successfully']);
        } catch (\Exception $exception) {
        }
    }
}
