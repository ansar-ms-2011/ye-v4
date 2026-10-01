<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\District;
use App\Models\RibiClub;
use App\Models\RibiCyeo;
use App\Models\RibiDyeo;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        $clubs = null;
        $districts = null;
        $user = $request->user();

        if ($user->hasRole('dyeo')) {
            $user->load('dyeo');
            $districts = District::select('id', 'code')->get();
        } elseif ($user->hasRole('cyeo')) {
            $user->load('cyeo');
            $clubs = RibiClub::select('id', 'club_name as name')->get();
        }

        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'cyeo' => $user->cyeo,
            'dyeo' => $user->dyeo,
            'clubs' => $clubs,
            'districts' => $districts,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        if ($request->user()->isAdmin()) {
            $request->user()->fill($request->validated());
            // if ($request->user()->isDirty('email')) {
            //    $request->user()->email_verified_at = null;
            // }
        }

        if ($request->user()->isCyeo()) {
            RibiCyeo::updateOrCreate([
                'user_id' => $request->user()->id,
            ], $request->validated());
            $request->user()->update([
                'full_name' => $request->validated()['cyeo_name'],
                'email' => $request->validated()['cyeo_email'],
                'rotary_club_id' => $request->validated()['ribi_club_id'],
            ]);
        }

        if ($request->user()->isDyeo()) {
            RibiDyeo::updateOrCreate([
                'user_id' => $request->user()->id,
            ], $request->validated());
            $request->user()->update([
                'full_name' => $request->validated()['dyeo_name'],
                'email' => $request->validated()['dyeo_email'],
                'district' => $request->validated()['district_code'],
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Your profile has been updated successfully.')]);

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
