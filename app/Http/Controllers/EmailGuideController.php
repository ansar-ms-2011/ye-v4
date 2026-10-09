<?php

namespace App\Http\Controllers;

use App\Mail\PaymentEmailCamps;
use App\Mail\PaymentEmailStep;
use App\Models\Application;
use App\Models\SentEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class EmailGuideController extends Controller
{
    public function GetEmailHistory(Application $application)
    {
        $emailSent = SentEmail::where('application_id', $application->id)->with('email_type')->get();
        $fileName = null;
        if ($application->exchange_type == 'CAMPS & TOURS') {
            $fileName = generateGuideFileCamps($application);
        } elseif ($application->exchange_type == 'STEP') {
            $fileName = generateGuideFileStep($application);
        }

        return Inertia::render('applications/EmailHistoryTable', [
            'application' => $application,
            'fileName' => $fileName,
            'emailSentList' => $emailSent,
            'appId' => $application->id
        ]);
    }

    public function SendPart1GuideThroughEmail(Application $application)
    {
        $emailSentList = sendGuidePart1ThroughEmail($application);

        return Inertia::flash('toast', ['message' => 'Guide (Part 1) has been emailed to applicant'])
            ->back()->with(['emailSentList' => $emailSentList]);
    }

    public function SendPart2GuideThroughEmail(Application $application)
    {
        $emailSentList = sendGuidePart2ThroughEmail($application);

        return Inertia::flash('toast', ['message' => 'Guide (Part 2) has been emailed to applicant'])
            ->back()->with(['emailSentList' => $emailSentList]);
    }

    public function SendPaymentEmailToApplicant(Application $application)
    {
        // Send Email to Applicant regarding payment of Administration Fee
        if ($application->exchange_type == 'CAMPS & TOURS') {
            Mail::send(new PaymentEmailCamps($application));
            SentEmail::create([
                'email_type_id' => 3,
                'application_id' => $application->id,
                'message_title' => 'Camps - Payment Email',
                'email_address' => $application->email_address,
            ]);
        } else {
            Mail::send(new PaymentEmailStep($application));
            SentEmail::create([
                'email_type_id' => 4,
                'application_id' => $application->id,
                'message_title' => 'STEP - Payment Email',
                'email_address' => $application->email_address,
            ]);
        }

        return Inertia::flash('toast', ['message' => 'Email has been sent to applicant for Payment'])
            ->back();
    }
}
