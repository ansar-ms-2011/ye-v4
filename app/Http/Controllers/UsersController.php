<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Models\District;
use App\Models\RibiClub;
use App\Models\RibiCyeo;
use App\Models\RibiDyeo;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

use function response;

class UsersController extends Controller
{
    public function EmailExists($email, $id)
    {
        $user = User::where('email', $email)->where('id', '!=', $id)->first();

        return response()->json(['result' => $user != null]);
    }

    public function index(Request $request)
    {
        $districts = District::query()
            ->get(['code as value', 'code as title'])
            ->toArray();

        $clubs = RibiClub::query()
            ->get(['id as value', 'club_name as title'])
            ->toArray();

        $roles = DB::table('roles')
            ->get(['id as value', 'name as title'])
            ->toArray();

        // --- Sorting ---
        $allowedSortColumns = ['id', 'full_name', 'email', 'district', 'created_at'];
        $orderBy = $request->input('sortBy.0', 'id');
        if (!in_array($orderBy, $allowedSortColumns, true)) {
            $orderBy = 'id';
        }

        $sortDir = $request->input('sortDesc.0') === 'true' ? 'desc' : 'asc';
        $searchText = $request->input('searchText', '');
        $perPage = (int)$request->input('perPage', 15);

        return Inertia::render('users/Index', [
            'users' => Inertia::defer(fn() => $this->prepareDeferredData($request)),
            'districts' => Inertia::once(fn() => $districts),
            'clubs' => Inertia::once(fn() => $clubs),
            'roles' => Inertia::once(fn() => $roles),
            'perPage' => $perPage,
            'searchText' => $searchText,
            'sortBy' => $orderBy,
            'sortDesc' => $sortDir === 'desc',
        ]);
    }

    public function store(UserStoreRequest $request)
    {
        $data = $request->all();
        $data['active'] = ($data['active'] == 'Yes' ? 1 : 0);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        if ($user && $data['role_id']) {
            $role = Role::find($data['role_id']);
            $user->syncRoles($role);
            $user->load('club');

            Inertia::flash('toast', ['type' => 'success', 'message' => 'User account created successfully.']);

            return redirect()->back();
        } else {
            return redirect()->back()->with('error', 'Something went wrong, please check again');
        }
    }

    public function update(UserStoreRequest $request, User $user)
    {
        try {
            $user->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'district' => $request->district,
                'rotary_club_id' => $request->rotary_club_id,
                'active' => ($request->active == 'Yes' ? 1 : 0),
            ]);

            $role = Role::find($request->role_id);
            if ($role) {
                $user->syncRoles($role);
            }

            if ($request->password) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            Inertia::flash('toast', ['type' => 'success', 'message' => 'User account Updated successfully.']);

            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->with('error', 'Something went wrong, please check again');
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            Inertia::flash('toast', ['type' => 'success', 'message' => 'User account deleted successfully.']);

            return redirect()->back();
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
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

        return response()->json(['message' => 'Users re-established for all CYEO accounts with default password 1...8']);
    }

    private function prepareDeferredData($request)
    {
        // --- Sorting ---
        $allowedSortColumns = ['id', 'full_name', 'email', 'district', 'created_at'];
        $orderBy = $request->input('sortBy.0', 'id');
        if (!in_array($orderBy, $allowedSortColumns, true)) {
            $orderBy = 'id';
        }

        $sortDir = $request->input('sortDesc.0') === 'true' ? 'desc' : 'asc';

        // --- Filters ---
        $searchText = $request->input('searchText', '');
        $perPage = (int)$request->input('perPage', 15);

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

        return $users;
    }
}
