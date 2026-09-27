<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\RibiClub;
use App\Models\RibiCyeo;
use App\Models\RibiDyeo;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UserController extends Controller
{
    public function EmailExists($email, $id)
    {
        $user = User::where('email', $email)->where('id', '!=', $id)->first();

        return response()->json(['result' => $user != null]);
    }

    public function index(Request $request)
    {
        $districts = District::query()
            ->get(['code as value', 'code as text'])
            ->toArray();

        $clubs = RibiClub::query()
            ->get(['club_name as text', 'id as value'])
            ->toArray();

        $roles = DB::table('roles')
            ->get(['id as value', 'name as text'])
            ->toArray();

        // --- Sorting ---
        $allowedSortColumns = ['id', 'full_name', 'email', 'district', 'created_at'];
        $orderBy = $request->input('sortBy.0', 'id');
        if (! in_array($orderBy, $allowedSortColumns, true)) {
            $orderBy = 'id';
        }

        $sortDir = $request->input('sortDesc.0') === 'true' ? 'desc' : 'asc';

        // --- Filters ---
        $searchText = $request->input('searchText', '');
        $showApplicant = $request->boolean('showApplicant');
        $perPage = (int) $request->input('per_page', 10);

        // --- Query ---
        $users = User::query()
            ->whereNull('application_id')
            ->when($searchText, function ($query) use ($searchText) {
                $query->where(function ($q) use ($searchText) {
                    $q->where('full_name', 'like', "%{$searchText}%")
                        ->orWhere('email', 'like', "%{$searchText}%")
                        ->orWhere('district', 'like', "%{$searchText}%");
                });
            })
            ->whereHas('roles', function ($q) {
                $q->where('name', '!=', 'applicant');
            })
            ->with(['club:id,club_name', 'roles:id,name'])
            ->orderBy($orderBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('users/Index', [
            'users' => $users,
            'districts' => $districts,
            'clubs' => $clubs,
            'roles' => $roles,
            'filters' => [
                'searchText' => $searchText,
                'showApplicant' => $showApplicant,
                'sortBy' => $orderBy,
                'sortDesc' => $sortDir === 'desc',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['active'] = ($data['active'] == 'Yes' ? 1 : 0);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        if ($user) {
            $user->syncRoles($data['role_id']);
            $user->load('club');

            return response()->json(['message' => 'User Created Successfully', 'user' => $user]);
        } else {
            abort(500, 'Something went wrong, please check again');
        }
    }

    public function update(Request $request, User $user)
    {
        $data = $request->all();
        $user->update($data);
        $user->syncRoles($data['role_id']);
        $user->load(['club', 'roles']);

        return response()->json(['message' => 'User Profile Updated Successfully', 'user' => $user]);
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            $user->delete();

            return response()->json(['message' => 'User Removed Successfully']);
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function GetProfile()
    {
        return view('profile');
    }

    public function SaveProfileData(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();
        Log::info('hjshd', [$data, $user->roles]);
        if ($user->hasRole('dyeo')) {
            RibiDyeo::updateOrCreate([
                'user_id' => $user->id,
            ], $data);

            return response()->json(['message' => 'Profile Updated For District YEO']);
        } elseif ($user->hasRole('cyeo')) {
            RibiCyeo::updateOrCreate([
                'user_id' => $user->id,
            ], $data);

            return response()->json(['message' => 'Profile Updated For Club YEO']);
        }
    }

    public function GetProfileData()
    {
        $user = auth()->user();
        $clubs = RibiClub::all(['id as value', 'club_name as text', 'district_code'])->toArray();
        $districts = District::pluck('code')->toArray();
        if ($user->hasRole('dyeo')) {
            $profile = RibiDyeo::where('user_id', $user->id)->first();
        } elseif ($user->hasRole('cyeo')) {
            $profile = RibiCyeo::where('user_id', $user->id)->first();
        }

        return response()->json([
            'profile' => $profile,
            'clubs' => $clubs,
            'districts' => $districts,
            'user' => $user,
            'role' => $user->roles[0]->name,
        ]);
    }

    public function usersTableClubsJson($district_code = null): JsonResponse
    {
        if ($district_code) {
            $clubs = RibiClub::where('district_code', $district_code)->orderBy('club_name')->get(['club_name as text', 'id as value']);
        } else {
            $clubs = RibiClub::orderBy('club_name')->get(['club_name as text', 'id as value']);
        }
        $clubs = $clubs->toArray();

        return response()->json(['clubs' => $clubs]);
    }

    public function reEstablishDyeoUsers()
    {
        $dyeos = RibiDyeo::all();
        foreach ($dyeos as $dyeo) {
            $user = User::where('email', $dyeo->dyeo_email)->first();
            // return $user;
            if ($user !== null) {
                $dyeo->user_id = $user->id;
                $dyeo->save();
            } else {
                $user = User::create([
                    'full_name' => $dyeo->dyeo_name,
                    'email' => $dyeo->dyeo_email,
                    'active' => true,
                    'password' => Hash::make('12345678'),
                    'district' => $dyeo->district_code,
                ]);
                $dyeo->user_id = $user->id;
                $dyeo->save();
            }
        }

        return response()->json(['message' => 'Users re-established for all DYEO accounts with default password 1...8']);
    }

    public function reEstablishCyeoUsers()
    {
        $cyeos = RibiCyeo::with(['club'])->get();
        foreach ($cyeos as $cyeo) {
            $user = User::where('email', $cyeo->cyeo_email)->first();
            // return $user;
            if ($user !== null) {
                $cyeo->user_id = $user->id;
                $cyeo->save();
            } elseif ($cyeo->cyeo_name && $cyeo->cyeo_email) {
                $user = User::create([
                    'full_name' => $cyeo->cyeo_name,
                    'email' => $cyeo->cyeo_email,
                    'active' => true,
                    'password' => Hash::make('12345678'),
                    'district' => $cyeo->club ? $cyeo->club->district_code : null,
                    'rotary_club_id' => $cyeo->ribi_club_id,
                ]);
                $cyeo->user_id = $user->id;
                $cyeo->save();
            }
        }

        return \response()->json(['message' => 'Users re-established for all CYEO accounts with default password 1...8']);
    }
}
