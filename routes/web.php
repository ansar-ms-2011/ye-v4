<?php

use App\Http\Controllers\ApplicantAuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CyeoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DyeoController;
use App\Http\Controllers\EmailGuideController;
use App\Http\Controllers\UsersController;
use App\Models\Application;
use App\Models\Media;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::resource('applicant', ApplicantAuthController::class)->only(['index', 'store']);

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UsersController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('users', UsersController::class)->only(['index', 'store', 'update', 'destroy'])->names('users');
        Route::resource('dyeos', DyeoController::class)->except(['create', 'show', 'edit']);
        Route::get('get-users-table-clubs/{district_code?}', [UsersController::class, 'usersTableClubsJson']);
        Route::get('check-email-exists/{email}/{id}', [UsersController::class, 'EmailExists']);
        Route::post('re-establish-dyeo-users', [UsersController::class, 'reEstablishDyeoUsers']);
        Route::post('re-establish-cyeo-users', [UsersController::class, 'reEstablishCyeoUsers']);
    });

    Route::group(['middleware' => ['role:admin|dyeo']], function () {
        Route::resource('clubs', ClubController::class)->except(['create', 'show', 'edit']);
        Route::resource('cyeos', CyeoController::class)->except(['create', 'show', 'edit']);
    });

    Route::group(['middleware' => ['role:admin|dyeo|applicant']], function () {
        Route::get('email-guide/{application}', [EmailGuideController::class, 'GetEmailHistory'])->name('email.emailHistory');
        Route::post('send-email-guide-part-1/{application}', [EmailGuideController::class, 'SendPart1GuideThroughEmail'])->name('email.sendPart1');
        Route::post('send-email-guide-part-2/{application}', [EmailGuideController::class, 'SendPart2GuideThroughEmail'])->name('email.sendPart2');
        Route::post('send-payment-email/{application}', [EmailGuideController::class, 'SendPaymentEmailToApplicant'])->name('email.sendPayment');

        Route::get('email-file/{id}.pdf', function ($id) {
            $fileName = 'app/'.$id.'.pdf';

            return response()->file(storage_path($fileName));
        })->name('email.file');
    });

    // Separated Application Edit route for applicant
    Route::group(['middleware' => ['role:admin|dyeo|cyeo|applicant']], function () {
        Route::resource('application', ApplicationController::class)->only(['index', 'edit', 'store', 'update', 'destroy']);
        Route::get('application/{id}/uploaded-files', [ApplicationController::class, 'ViewUploadedFiles'])->name('application.uploadedFiles');

        Route::post('remove-uploaded-media/{media}', [ApplicationController::class, 'removeUploadedMedia'])->name('application.removeMedia');
    });


    Route::group(['middleware' => ['role:admin|cyeo|dyeo|applicant']], function () {
        Route::get('application-pdf-view/{id}', function ($id) {
            $application = Application::find($id);
            // Check Access By the Right User
            $user = auth()->user();
            //  Check Access By the Right User otherwise abort request
            checkApplicationAccess($application, $user);
            // If Access is valid, then prepare PDF documents and show to user
            $application->load([
                'club',
                'dyeo',
                'languages',
                'address_home',
                'address_postal',
                'address_emergency',
                'address_parent1',
                'address_parent2']);
            if ($application->exchange_type == 'CAMPS & TOURS') {
                return get_CAMPS_Pdf_v2($application)->Output();

            } else {
                return get_STEP_Pdf_v2($application)->Output();
            }

        })->name('application.fullPdfView');

        Route::get('view-signing-page/{id}', function ($id) {
            $application = Application::find($id);
            // Check Access By the Right User
            $user = auth()->user();
            //  Check Access By the Right User otherwise abort request
            checkApplicationAccess($application, $user);
            // If Access is valid, then prepare PDF documents and show to user
            $application->load([
                'club',
                'dyeo',
                'languages',
                'address_home',
                'address_postal',
                'address_emergency',
                'address_parent1',
                'address_parent2']);

            if ($application->exchange_type == 'CAMPS & TOURS') {
                return get_CAMPS_Signing_Page($application)->Output();

            } else {
                return get_STEP_Signing_Page_3_5_6($application)->Output();
            }

        })->name('application.signingPageView');

        Route::get('view-signing-page-5-6/{id}', function ($id) {
            $application = Application::find($id);
            $user = auth()->user();
            //  Check Access By the Right User otherwise abort request
            checkApplicationAccess($application, $user);
            // If Access is valid, then prepare PDF documents and show to user
            $application->load([
                'club',
                'dyeo',
                'languages',
                'address_home',
                'address_postal',
                'address_emergency',
                'address_parent1',
                'address_parent2']);

            return get_CAMPS_Signing_Page_5_6($application)->Output();
        })->name('application.signingPageView56');
    });
});

Route::get('application/create', [ApplicationController::class, 'create'])->name('application.create');

Route::get('get-media-file/{media}', function (Media $media) {
    return response()->file(storage_path('app/'.$media->media_path));
});

Route::get('/update-app', function () {
    Artisan::call('dump-autoload');
    echo 'dump-autoload complete';
});

Route::get('/view-part-2/{application}', function (Application $application) {
    if ($application->exchange_type == 'CAMPS & TOURS') {
        $file = generateGuidePart2FileCamps($application);
    } else {
        $file = generateGuidePart2FileStep($application);
    }

    return response()->file(storage_path('app/'.$file));
});

Route::get('/update-media', function () {
    $applications = Application::where('media_id', '>', 0)->get();
    foreach ($applications as $application) {
        $media = Media::find($application->media_id);
        $media_category = DB::table('media_categories')->where('category_name', 'Photo-Self')->first();
        if ($media) {
            $media->application_id = $application->id;
            $media->media_category_label = $media_category->category_name;
            $media->media_category_id = $media_category->id;
            $media->save();
        }
    }

    return 'All media files updated successfully';
});

require __DIR__.'/settings.php';

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');

    return 'Application cache cleared';
});
