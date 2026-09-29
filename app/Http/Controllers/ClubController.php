<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClubFormRequest;
use App\Models\District;
use App\Models\RibiClub;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $districts = District::when($request->user()->hasAnyRole(['dyeo']), function ($query) use ($request) {
            $query->where('code', $request->user()->district);
        })->select(['id', 'code'])->get()->toArray();

        $user = auth()->user();
        $role = $user->roles[0]->name;
        $orderBy = $request->get('sortBy');
        $orderBy = $orderBy ? $orderBy[0] : 'id';
        $sortDir = $request->get('sortDesc');
        $sortDir = ($sortDir && $sortDir[0] == 'true') ? 'desc' : 'asc';
        $searchText = $request->get('searchText');

        $perPage = $request->get('itemsPerPage');
        // If dyeo is looking for clubs, then only show club associated with dyeo district
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
            })->orderBy($orderBy, $sortDir)
                ->paginate($perPage ?? 15);
        }

        return Inertia::render('clubs/Index', [
            'districts' => $districts,
            'clubs' => $clubs,
        ]);
    }

    /**
     * @throws Exception
     */
    public function store(ClubFormRequest $request)
    {
        $data = $request->all();

        try {
            RibiClub::create([
                'id' => DB::table('ribi_clubs')->max('id') + 1,
                'district_code' => DB::table('districts')->find($data['district_id'])?->code,
                ...$data,
            ]);

            Inertia::flash('toast', ['type' => 'success', 'message' => 'New club created successfully.']);

            return redirect()->back();
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return redirect()->back()->with('error', 'Something went wrong, please check again');
        }
    }

    public function update(RibiClub $club, ClubFormRequest $request)
    {
        try {
            $club->update([
                'district_code' => DB::table('districts')->find($request->district_id)?->code,
                ...$request->all(),
            ]);

            Inertia::flash('toast', ['type' => 'success', 'message' => 'Club Updated successfully.']);

            return redirect()->back();
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => 'An error has been occurred, please check again']);

            return redirect()->back();
        }
    }

    public function destroy($club)
    {
        $club = RibiClub::findOrfail($club)->delete();

        return response()->json(['message' => 'Club Deleted successfully']);
    }
}
