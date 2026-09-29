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

        $perPage = $request->perPage;

        $searchText = $request->input('searchText');
        Log::info('searchText: '.$searchText);
        $clubs = RibiClub::query()
            ->when($request->user()->hasRole('dyeo'), function ($q) use ($request) {
                $q->where('district_code', $request->user()->district);
            })
            ->when($searchText, function ($q) use ($searchText) {
                $q->where(function ($query) use ($searchText) {
                    $query->where('club_name', 'like', "%{$searchText}%")
                        ->orWhere('district_code', 'like', "%{$searchText}%")
                        ->orWhere('club_president', 'like', "%{$searchText}%")
                        ->orWhere('club_president_email', 'like', "%{$searchText}%")
                        ->orWhere('club_president_mobile', 'like', "%{$searchText}%");
                });
            })
            ->paginate($perPage ?? 15)
            ->withQueryString();

        return Inertia::render('clubs/Index', [
            'districts' => $districts,
            'clubs' => $clubs,
            'filters' => [
                'searchText' => $searchText ?? '',
            ],
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
        try {
            RibiClub::findOrfail($club)->delete();

            Inertia::flash('toast', ['type' => 'success', 'message' => 'Club Deleted successfully.']);

            return redirect()->back();
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => 'An error has been occurred, please check again']);

            return redirect()->back();
        }
    }
}
