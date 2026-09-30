<?php

use App\Mail\SendEmailGuideConfirm;
use App\Mail\SendEmailGuidePart1;
use App\Mail\SendEmailGuidePart2;
use App\Models\Application;
use App\Models\District;
use App\Models\RibiClub;
use App\Models\RibiCyeo;
use App\Models\RibiDyeo;
use App\Models\SentEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use setasign\Fpdi\Fpdi;

function createDirectory($path, $clear = true): void
{
    if (! file_exists($path)) {
        mkdir($path, 0777, true);
    }
    // Clear all files when clear flag is true
    if ($clear) {
        $files = glob($path.'/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
    }
}

function SaveApplicationMediaFiles(Application $application)
{
    $application->load('media_library');
    $media_library = $application->media_library;
    if ($media_library) {
        // if not already exists, create folder to store images with helper function.
        $path = storage_path('app/media-library/'.$application->id);
        createDirectory($path);
        foreach ($media_library as $media) {
            $image = Image::make($media->media);
            $image->save(storage_path('app/media-library/'.$application->id.'/'.$media->media_category_label.'.png'));
        }
    }
}

function checkApplicationAccess($application, $user)
{
    if ($user->hasRole('applicant') && $user->application_id != $application->id) {
        abort(403, 'Un-Authorized Access by Applicant');
    }

    if ($user->hasRole('dyeo')) {
        $dyeo = RibiDyeo::where('user_id', $user->id)->first();
        if (! $dyeo || $dyeo->id !== $application->dyeo_id) {
            abort(403, 'Un-Authorized Access by District YEO');
        }
    }
    if ($user->hasRole('cyeo')) {
        $cyeo = RibiCyeo::where('user_id', $user->id)->first();
        Log::info('cyeo: ', [$cyeo, $application->rotary_club_id]);
        if (! $cyeo || ! $application->rotary_club_id || $cyeo->ribi_club_id !== $application->rotary_club_id) {
            abort(403, 'Un-Authorized Access by Club YEO');
        }
    }
    // allowed all for system admin role
}

function getAppClass($app_date): string
{
    $app_date->setHour(0);
    $app_date->setMinute(0);
    $app_date->setSecond(0);

    $current_month = now()->month;
    $current_year = now()->year;

    if ($current_month >= 9) {
        $current_season_start = Carbon::create($current_year, 9);
    } else {
        $current_season_start = Carbon::create($current_year - 1, 9);
    }
    if ($app_date < $current_season_start) {
        return 'previous_season';
    } else {
        return 'current_season';
    }
}

function generateGuideFileCamps($application): string
{
    $pdf = new Fpdi;
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->guide_name);
    $templateGuide = storage_path('app/pdf-templates/guide_camps_part_1v2.pdf');    // Updated on 02-Nov-2024
    $pageCount = $pdf->setSourceFile($templateGuide);
    $fileName = $application->application_no.'-guide-part-1.pdf';

    $pdf->SetFont('Arial', 'B', 25);

    $tplIdx = $pdf->importPage(1);
    $pdf->addPage();
    $pdf->useTemplate($tplIdx, 0, 0);

    $pdf->SetXY(60, 157);
    $pdf->MultiCell(87, 10, $iconv_name, 0, 'C');

    if ($application->dyeo != null) {
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetXY(110, 180);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $pdf->SetXY(114, 194);
    $pdf->Write(0, $application->application_no);
    // Import pages from 2-6 as is, no personalization is required
    for ($i = 2; $i < 7; $i++) {
        $tplIdx = $pdf->importPage($i);
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, 0, 0);
    }
    // Import pages from 7-9 as is, personalization is required
    $pdf->SetFont('Arial', 'B', 14);
    for ($i = 7; $i < 10; $i++) {
        $tplIdx = $pdf->importPage($i);
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, 0, 0);
        // Applicant Name
        $pdf->SetXY(140, 14);
        $pdf->Write(0, $iconv_name);
        // District Number
        if ($application->dyeo != null) {
            $pdf->SetXY(140, 21);
            $pdf->Write(0, $application->dyeo->district_code);
        }
    }

    $pdf->Output('F', storage_path('app/'.$fileName));

    return $fileName;
}

function generateGuideFileStep($application): string
{
    $pdf = new Fpdi;
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->guide_name);
    $templateGuide = storage_path('app/pdf-templates/guide_step_part_1v2.pdf');    // Updated on 02-Nov-2024
    $pageCount = $pdf->setSourceFile($templateGuide);
    $fileName = $application->application_no.'-guide-part-1.pdf';

    $pdf->SetFont('Arial', 'B', 25);

    $tplIdx = $pdf->importPage(1);
    $pdf->addPage();
    $pdf->useTemplate($tplIdx, 0, 0);

    $pdf->SetXY(64, 153);
    $pdf->MultiCell(82, 10, $iconv_name, 0, 'C');
    // District Number
    if ($application->dyeo != null) {
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetXY(110, 176);
        $pdf->Write(0, $application->dyeo->district_code);
    }
    // Application Number
    $pdf->SetXY(120, 190);
    $pdf->Write(0, $application->application_no);
    // Import pages from 2-6 as is, no personalization is required
    for ($i = 2; $i <= 6; $i++) {
        $tplIdx = $pdf->importPage($i);
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, 0, 0);
    }
    // Import pages from 7-9 as is, personalization is required
    $pdf->SetFont('Arial', 'B', 14);
    for ($i = 7; $i <= 9; $i++) {
        $tplIdx = $pdf->importPage($i);
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, 0, 0);
        // Applicant Name
        $pdf->SetXY(140, 14);
        $pdf->Write(0, $iconv_name);
        // District Number
        if ($application->dyeo != null) {
            $pdf->SetXY(140, 21);
            $pdf->Write(0, $application->dyeo->district_code);
        }
    }

    $pdf->Output('F', storage_path('app/'.$fileName));

    return $fileName;
}

function generateGuidePart2FileCamps(Application $application): string
{
    $pdf = new Fpdi;
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->guide_name);
    $templateGuide = storage_path('app/pdf-templates/guide_camps_part_2.pdf');
    $pageCount = $pdf->setSourceFile($templateGuide);
    $fileName = $application->application_no.'-guide-part-2.pdf';

    $pdf->SetFont('Arial', 'B', 25);

    $tplIdx = $pdf->importPage(1);
    $pdf->addPage();
    $pdf->useTemplate($tplIdx, 0, 0);

    $pdf->SetXY(60, 158);
    $pdf->MultiCell(87, 10, $iconv_name, 0, 'C');

    if ($application->dyeo != null) {
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetXY(114, 180);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $pdf->SetXY(114, 194);
    $pdf->Write(0, $application->application_no);

    // Import pages from 2-6 as is, no personalization is required
    for ($i = 2; $i <= 9; $i++) {
        $tplIdx = $pdf->importPage($i);
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, 0, 0);
    }
    // Import pages from 7-9 as is, personalization is required
    $pdf->SetFont('Arial', 'B', 14);
    for ($i = 10; $i <= 13; $i++) {
        $tplIdx = $pdf->importPage($i);
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, 0, 0);
        // Applicant Name
        $pdf->SetXY(140, 14);
        $pdf->Write(0, $iconv_name);
        // District Number
        if ($application->dyeo != null) {
            $pdf->SetXY(140, 21);
            $pdf->Write(0, $application->dyeo->district_code);
        }
    }

    $pdf->Output('F', storage_path('app/'.$fileName));

    return $fileName;
}

function generateGuidePart2FileStep(Application $application): string
{
    $pdf = new Fpdi;
    $iconv_name = iconv('UTF-8', 'ISO-8859-1', $application->guide_name);
    $templateGuide = storage_path('app/pdf-templates/guide_step_part_2.pdf');
    $pageCount = $pdf->setSourceFile($templateGuide);
    $fileName = $application->application_no.'-guide-part-2.pdf';

    $pdf->SetFont('Arial', 'B', 25);

    $tplIdx = $pdf->importPage(1);
    $pdf->addPage();
    $pdf->useTemplate($tplIdx, 0, 0);

    $pdf->SetXY(60, 158);
    $pdf->MultiCell(87, 10, $iconv_name, 0, 'C');

    if ($application->dyeo != null) {
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetXY(114, 180);
        $pdf->Write(0, $application->dyeo->district_code);
    }

    $pdf->SetXY(114, 194);
    $pdf->Write(0, $application->application_no);

    // Import pages from 2-9 as is, no personalization is required
    for ($i = 2; $i <= 9; $i++) {
        $tplIdx = $pdf->importPage($i);
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, 0, 0);
    }
    // Import pages from 10-13, personalization is required
    $pdf->SetFont('Arial', 'B', 14);
    for ($i = 10; $i <= 13; $i++) {
        $tplIdx = $pdf->importPage($i);
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, 0, 0);
        // Applicant Name
        $pdf->SetXY(140, 14);
        $pdf->Write(0, $iconv_name);
        // District Number
        if ($application->dyeo != null) {
            $pdf->SetXY(140, 21);
            $pdf->Write(0, $application->dyeo->district_code);
        }
    }

    $pdf->Output('F', storage_path('app/'.$fileName));

    return $fileName;
}

function sendGuidePart1ThroughEmail($application)
{
    if ($application->exchange_type === 'CAMPS & TOURS') {
        $type = 'CAMPS';
        generateGuideFileCamps($application);
        Mail::send(new SendEmailGuidePart1($application, $type));
    } else {
        $type = 'STEP';
        generateGuideFileStep($application);
        Mail::send(new SendEmailGuidePart1($application, $type));
    }

    // Send Email to DYEO that guide has been sent to applicant
    if ($application->dyeo != null) {
        Mail::send(new SendEmailGuideConfirm($application, $type));
    }

    // Create entry in Database table for Sent Email
    SentEmail::create([
        'email_type_id' => $type === 'CAMPS' ? 1 : 2,
        'application_id' => $application->id,
        'message_title' => 'Guide (Part 1) to International CAMPS - '.$application->full_name,
        'email_address' => $application->email_address,
    ]);

    return SentEmail::where('application_id', $application->id)->with('email_type')->get();
}

function sendGuidePart2ThroughEmail($application)
{
    if ($application->exchange_type === 'CAMPS & TOURS') {
        $type = 'CAMPS';
        generateGuidePart2FileCamps($application);
        Mail::send(new SendEmailGuidePart2($application, $type));
    } else {
        $type = 'STEP';
        generateGuidePart2FileStep($application);
        Mail::send(new SendEmailGuidePart2($application, $type));
    }

    // Send Email to DYEO that guide has been sent to applicant
    if ($application->dyeo != null) {
        Mail::send(new SendEmailGuideConfirm($application, $type));
    }

    // Create entry in Database table for Sent Email
    SentEmail::create([
        'email_type_id' => $type === 'CAMPS' ? 1 : 2,
        'application_id' => $application->id,
        'message_title' => 'Guide (Part 2) to International '.$type.' - '.$application->full_name,
        'email_address' => $application->email_address,
    ]);

    return SentEmail::where('application_id', $application->id)->with('email_type')->get();
}

function printTelNos($pdf, $x, $y, $tel, $mobile, $btel)
{
    if (! empty($tel) && empty($mobile) && empty($btel)) {
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY($x, $y + 1);
        $pdf->Write(0, $tel);
    }

    if (empty($tel) && ! empty($mobile) && empty($btel)) {
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY($x, $y + 1);
        $pdf->Write(0, $mobile);
    }

    if (empty($tel) && empty($mobile) && ! empty($btel)) {
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY($x, $y + 1);
        $pdf->Write(0, $btel);
    }
    $line_inc = 3;
    if (! empty($tel) && ! empty($mobile) && empty($btel)) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($x, $y);
        $pdf->Write(0, $tel);
        $pdf->SetXY($x, $y + $line_inc);
        $pdf->Write(0, $mobile);
    }

    if (! empty($tel) && empty($mobile) && ! empty($btel)) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($x, $y);
        $pdf->Write(0, $tel);
        $pdf->SetXY($x, $y + $line_inc);
        $pdf->Write(0, $btel);
    }

    if (empty($tel) && ! empty($mobile) && ! empty($btel)) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY($x, $y);
        $pdf->Write(0, $mobile);
        $pdf->SetXY($x, $y + $line_inc);
        $pdf->Write(0, $btel);
    }
    $line_inc = 2;
    if (! empty($tel) && ! empty($mobile) && ! empty($btel)) {
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetXY($x, $y);
        $pdf->Write(0, $tel);
        $pdf->SetXY($x, $y + $line_inc);
        $pdf->Write(0, $mobile);
        $pdf->SetXY($x, $y + $line_inc * 2);
        $pdf->Write(0, $btel);
    }
    $pdf->SetFont('Arial', '', 10);

    return $pdf;
}

function getOnceDistricts(Request $request)
{
    return District::when($request->user()
        ->hasAnyRole(['dyeo']), function ($query) use ($request) {
            $query->where('code', $request->user()->district);
        })->select(['id', 'code'])->get()->toArray();
}

function getOnceClubs(Request $request)
{
    return RibiClub::select(['id', 'club_name', 'district_id'])
        ->get()->toArray();
}
