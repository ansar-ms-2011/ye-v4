<?php

namespace App\Http\Controllers;

use App\Http\Requests\CyeoStoreFormRequest;
use App\Models\District;
use App\Models\RibiClub;
use App\Models\RibiCyeo;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CyeoController extends Controller
{
    public function index(Request $request)
    {
        $districts = District::when($request->user()
            ->hasAnyRole(['dyeo']), function ($query) use ($request) {
            $query->where('code', $request->user()->district);
        })->select(['id', 'code'])->get()->toArray();

        $clubs = RibiClub::select(['id', 'club_name', 'district_id'])
            ->get()->toArray();

        $searchText = $request->input('searchText');

        return Inertia::render('cyeos/Index', [
            'cyeoPaginator' => Inertia::defer(fn() => $this->prepareDeferredData($request), rescue: true),
            'districts' => Inertia::once(fn() => $districts),
            'clubs' => Inertia::once(fn() => $clubs),
            'filters' => [
                'searchText' => $searchText ?? '',
            ],
        ]);
    }

    public function cyeoJsonData()
    {
        $ribi_clubs = RibiClub::orderBy('club_name')->get()->groupBy('district_code')->toArray();
        $districts = DB::table('districts')->get('code')->toArray();
        $districts = array_column($districts, 'code');

        return response()->json([
            'districts' => $districts,
            'ribi_clubs' => $ribi_clubs,
        ]);
    }

    public function store(CyeoStoreFormRequest $request)
    {
        try {
            $cyeo = RibiCyeo::create($request->all());

            $user = User::create([
                'full_name' => $cyeo->cyeo_name,
                'email' => $cyeo->cyeo_email,
                'active' => true,
                'password' => Hash::make('12345678'),
                'rotary_club_id' => $cyeo->ribi_club_id,
            ]);
            $user->assignRole('cyeo');
            $cyeo->update(['user_id' => $user->id]);

            Inertia::flash('toast', ['type' => 'success', 'message' => 'Club youth exchange officer created successfully.']);

            return redirect()->back();
        } catch (Exception $e) {
            Log::error('Error creating user account: ' . $e->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => 'An error occurred while creating the club youth exchange officer.']);

            return redirect()->back();
        }
    }

    public function update(RibiCyeo $cyeo, Request $request)
    {
        try {
            $data = $request->all();
            $cyeo->update($data);
            $cyeo->user()?->update([
                'full_name' => $request->cyeo_name,
                'email' => $request->cyeo_email,
            ]);

            Inertia::flash('toast', ['type' => 'success', 'message' => 'CYEO updated successfully.']);

            return redirect()->back();
        } catch (Exception $exception) {
            Log::error('Error updating CYEO: ' . $exception->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => 'An error occurred while updating the CYEO.']);

            return redirect()->back();
        }
    }

    public function destroy(RibiCyeo $cyeo)
    {
        try {
            $cyeo->user()?->delete();
            $cyeo->delete();

            Inertia::flash('toast', ['type' => 'success', 'message' => 'CYEO removed successfully.']);

            return redirect()->back();
        } catch (Exception $exception) {
            Log::error('Error deleting club youth exchange officer: ' . $exception->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => 'An error occurred while deleting the club youth exchange officer.']);

            return redirect()->back();
        }
    }

    private function prepareDeferredData(Request $request)
    {
        $searchText = $request->input('searchText');
        $perPage = $request->input('itemsPerPage');

        if ($request->user()->hasRole('dyeo')) {
            $cyeosPaginator = RibiCyeo::whereHas('club', function (Builder $query) {
                $query->where('district_code', request()->user()->district);
            })
                ->where('cyeo_name', 'like', "%$searchText%")
                ->with(['club' => function ($q) {
                    $q->select('id', 'club_name', 'district_id')
                        ->with('district:id,code');
                }])
                ->paginate($perPage ?? 15);
        } else {
            $cyeosPaginator = RibiCyeo::where('cyeo_name', 'like', "%$searchText%")
                ->orWhereHas('club', function (Builder $query) use ($searchText) {
                    $query->where('club_name', 'like', "%$searchText%");
                    $query->orWhere('district_code', 'like', "%$searchText%");
                })
                ->with(['club' => function ($q) {
                    $q->select('id', 'club_name', 'district_id')
                        ->with('district:id,code');
                }])
                ->paginate($perPage ?? 15);
        }

        return $cyeosPaginator;
    }
}
