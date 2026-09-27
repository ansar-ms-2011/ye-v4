<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicantLoginStoreRequest;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class ApplicantAuthController extends Controller
{
    public function storeLogin(ApplicantLoginStoreRequest $request) : RedirectResponse
    {
        $app = Application::with(['user'])->where(function ($query) use ($request) {
            $query->where('application_no', $request->application_no)
                ->where('dob', $request->applicant_dob);
        })->first();
        if ($app) {
            if (! $app->user) {
                $user = User::create([
                    'full_name' => $app->firstname,
                    'email' => $app->id.'_'.$app->email_address,      // To allow multiple applications from same user
                    'password' => Hash::make('applicant'),
                    'application_id' => $app->id,
                    'active' => 1,
                ]);
                $user->assignRole('applicant');
                $app->user()->save($user);
            }
            auth()->loginUsingId($app->user_id);
        } else {
            return redirect()->back()->withErrors(['applicant_login_error' => 'Invalid Application Number / Date Of Birth']);
        }

        return redirect('application/'.$app->id.'/edit');
    }
}
