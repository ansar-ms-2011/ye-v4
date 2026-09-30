<?php

namespace App\Http\Controllers;

use App\Http\Requests\DyeoStoreFormRequest;
use App\Models\RibiClub;
use App\Models\RibiDyeo;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DyeoController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('dyeos/Index', [
            'dyeoPaginator' => Inertia::defer(fn () => $this->getDeferredData($request), rescue: true),
            'districts' => Inertia::once(fn () => getOnceDistricts($request)),
        ]);
    }

    private function getDeferredData(Request $request)
    {
        $searchText = $request->input('searchText');
        $perPage = $request->input('itemsPerPage');

        return RibiDyeo::where('dyeo_name', 'like', "%$searchText%")
            ->orWhere('district_code', 'like', "%$searchText%")
            ->orWhere('dyeo_city', 'like', "%$searchText%")
            ->paginate($perPage ?? 15);
    }

    public function dyeoJsonData()
    {
        $ribi_clubs = RibiClub::orderBy('club_name')->get()->toArray();
        $districts = DB::table('districts')->get(['id', 'code'])->toArray();
        $districts = array_column($districts, 'code');

        return response()->json([
            'districts' => $districts,
            'ribi_clubs' => $ribi_clubs,
        ]);
    }

    public function store(DyeoStoreFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $dyeo = RibiDyeo::create($request->all());
            $user = User::create([
                'full_name' => $dyeo->dyeo_name,
                'email' => $dyeo->dyeo_email,
                'active' => true,
                'password' => Hash::make('12345678'),
                'district' => $dyeo->district_code,
            ]);
            $user->assignRole('dyeo');
            $dyeo->update(['user_id' => $user->id]);
            DB::commit();
            Inertia::flash('toast', ['type' => 'success', 'message' => 'District youth exchange officer created successfully.']);

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => $e->getMessage()]);

            return redirect()->back();
        }
    }

    public function update(RibiDyeo $dyeo, DyeoStoreFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $dyeo->update($request->all());
            $dyeo->user()->update([
                'full_name' => $request->dyeo_name,
                'email' => $request->email,
                'password' => Hash::make('12345678'),
            ]);

            DB::commit();

            Inertia::flash('toast', ['type' => 'success', 'message' => 'District youth exchange officer updated successfully.']);

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => $e->getMessage()]);

            return redirect()->back();
        }
    }

    public function destroy(RibiDyeo $dyeo)
    {
        try {
            DB::beginTransaction();
            $dyeo->user()->delete();
            $dyeo->delete();
            DB::commit();
            Inertia::flash('toast', ['type' => 'success', 'message' => 'District youth exchange officer deleted successfully.']);

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            Inertia::flash('toast', ['type' => 'error', 'message' => $e->getMessage()]);

            return redirect()->back();
        }
    }
}
