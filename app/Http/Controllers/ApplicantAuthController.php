<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicantLoginStoreRequest;
use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ApplicantAuthController extends Controller
{
    public function index()
    {
        return Inertia::render('auth/ApplicantLogin', []);
    }

    public function store(ApplicantLoginStoreRequest $request)
    {
        $dob = Carbon::createFromFormat('d-m-Y', $request->applicant_dob)->format('Y-m-d');

        $app = Application::with('user')
            ->where('application_no', $request->application_no)
            ->whereDate('dob', $dob)
            ->first();

        if (! $app) {
            return back()->withErrors([
                'application_no' => 'These credentials do not match our records.',
            ]);
        }

        if (! $app->user) {
            $user = User::create([
                'full_name' => $app->firstname,
                'email' => $app->id.'_'.$app->email_address,
                'password' => Hash::make('applicant'),
                'application_id' => $app->id,
                'active' => 1,
            ]);
            $user->assignRole('applicant');
            $app->setRelation('user', $user);
        }

        auth()->login($app->user);

        return redirect()->route('dashboard');
    }
}
