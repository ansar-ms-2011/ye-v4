<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationAddress;
use App\Models\ApplicationLanguage;
use App\Models\ApplicationSibling;
use App\Models\Media;
use App\Models\RibiClub;
use App\Models\RibiDyeo;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $searchText = $request->input('searchText');
        $sortBy = $request->input('sortBy') ?? 'application_no';
        $sortOrder = $request->input('sortOrder') ?? 'desc';
        $perPage = $request->input('itemsPerPage');
        $applications = Application::query();

        $applications->when($searchText, function ($q) use ($searchText) {
            $q->where(function (Builder $query) use ($searchText) {
                $query->where('firstname', 'like', "%$searchText%")
                    ->orWhere('surname', 'like', "%$searchText%")
                    ->orWhere('application_status', 'like', "%$searchText%")
                    ->orWhere('application_no', 'like', "%$searchText%");
            })->orWhereHas('dyeo', function ($q) use ($searchText) {
                $q->where('district_code', 'LIKE', "%$searchText%");
            });
        });

        // Role-Based Filtering of Applications
        if ($user->hasRole('dyeo')) {
            $dyeo = RibiDyeo::where('user_id', $user->id)->first();
            if (! $dyeo) {
                return response()->json([
                    'success' => false,
                    'message' => 'No DYEO associated with User Id '.$user->id,
                ]);
            }
            $applications = $applications->where('dyeo_id', $dyeo->id);
        } elseif ($user->hasRole('cyeo')) {
            $user->load('cyeo');
            if ($user->cyeo && $user->cyeo->ribi_club_id) {
                $applications->where('rotary_club_id', $user->cyeo->ribi_club_id);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "No applications found associated with your club {$user->cyeo?->club?->club_name} or you don't have associated to any club",
                ]);
            }
        } elseif ($user->hasRole('applicant')) {
            $applications->whereNull('id'); // Just a workaround to hide applications
        }

        $applicationsPaginator = $applications->with([
            'dyeo:id,dyeo_name,district_code',
        ])
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage ?? 15);

        return Inertia::render('applications/Index', [
            'applicationsPaginator' => $applicationsPaginator,
        ]);
    }

    public function create()
    {
        $dyeos = RibiDyeo::all(['id', 'id as value', 'dyeo_name as text', 'district_code'])->toArray();
        $clubs = RibiClub::all(['id as value', 'club_name as text', 'district_code']);
        $clubs = $clubs->groupBy('district_code');

        return Inertia::render('applications/Create', [
            'dyeos' => $dyeos, 'clubs' => $clubs,
        ]);
    }

    public function show(Application $application)
    {
        return Inertia::render('applications/Edit', ['application' => $application]);
    }

    public function edit(Application $application)
    {
        $user = auth()->user();
        checkApplicationAccess($application, $user);

        $dyeos = RibiDyeo::all(['id', 'id as value', 'dyeo_name as text', 'district_code'])->toArray();
        $clubs = RibiClub::all(['id as value', 'club_name as text', 'district_code']);
        $clubs = $clubs->groupBy('district_code');
        $application->load([
            'siblings',
            'languages',
            'address_home',
            'address_postal',
            'address_emergency',
            'address_parent1',
            'address_parent2',
            'dyeo',
            'media_library',
        ]);

        return Inertia::render('applications/Edit', [
            'application' => $application,
            'dyeos' => $dyeos,
            'clubs' => $clubs,
        ]);
    }

    public function update(Request $request, Application $application)
    {
        $user = auth()->user();
        checkApplicationAccess($application, $user);

        $formData = $request->all();
        $validated = $request->validate([
            'media_library.*.brief_caption' => 'required_with:media_library.*.media|max:150',
            'media_library.*.size' => 'max:5120',
        ]);

        // Truncate Medical Info if > 650
        $info = $formData['medical_info'];
        $info = (strlen($info) > 650) ? substr($info, 0, 650) : $info;
        $formData['medical_info'] = $info;
        // ---------------------------------
        $application->update($formData);

        $formData['address_home']['application_id'] = $application->id;
        $formData['address_emergency']['application_id'] = $application->id;
        $formData['address_parent1']['application_id'] = $application->id;
        $formData['address_parent2']['application_id'] = $application->id;
        $formData['address_postal']['application_id'] = $application->id;

        ApplicationAddress::updateOrCreate(['id' => $formData['address_home']['id'], 'application_id' => $application->id], $formData['address_home']);
        ApplicationAddress::updateOrCreate(['id' => $formData['address_emergency']['id'], 'application_id' => $application->id], $formData['address_emergency']);
        ApplicationAddress::updateOrCreate(['id' => $formData['address_postal']['id'], 'application_id' => $application->id], $formData['address_postal']);
        ApplicationAddress::updateOrCreate(['id' => $formData['address_parent1']['id'], 'application_id' => $application->id], $formData['address_parent1']);
        ApplicationAddress::updateOrCreate(['id' => $formData['address_parent2']['id'], 'application_id' => $application->id], $formData['address_parent2']);

        $languages = $formData['languages'];
        foreach ($languages as $language) {
            if ($language['remove'] == true && $language['id'] != null) {
                ApplicationLanguage::destroy($language['id']);
            } else {
                $language['application_id'] = $application->id;
                $language['application_no'] = $application->application_no;
                ApplicationLanguage::updateOrCreate(['id' => $language['id']], $language);
            }
        }

        $siblings = $formData['siblings'];
        foreach ($siblings as $sibling) {
            if ($sibling['remove'] == true && $sibling['id'] != null) {
                ApplicationSibling::destroy($sibling['id']);
            } else {
                $sibling['application_id'] = $application->id;
                // $sibling['application_no'] = $application->application_no;
                ApplicationSibling::updateOrCreate(['id' => $sibling['id']], $sibling);
            }
        }

        // Handle Profile Image Upload / Change
        if ($formData['image_changed']) {
            if ($formData['media_id'] > 0) {
                // Delete previous linked media row
                Media::find($formData['media_id'])->delete();
            }
            $obj = Media::create(['media' => $formData['image_data'], 'created_at' => now()]);
            $application->media_id = $obj->id;
            $application->save();
        }
        // Handle Media Library Section
        // Log::info($formData['media_library']);
        if ($formData['media_library']) {
            foreach ($formData['media_library'] as $media) {
                if ($media['media']) {
                    $media['application_id'] = $application->id;
                    $media['media_category_id'] = DB::table('media_categories')->where('category_name', $media['media_category_label'])->value('id');
                    unset($media['size']);
                    unset($media['changed']);
                    Media::updateOrCreate(['id' => $media['id']], $media);
                }
            }
        }
        $application->load('media_library');

        return redirect()->back()->with('message', 'Application Updated Successfully');
    }

    public function removeUploadedMedia(Media $media)
    {
        $media->delete();

        return redirect()->back()->with('message', 'Media File Removed Successfully');
    }

    public function ViewUploadedFiles($id)
    {
        $media_library = Media::where('application_id', $id)->get();

        return Inertia::render('applications/UploadedFiles', ['files' => $media_library, 'application_id' => $id]);
    }

    /**
     * @throws Exception
     */
    public function destroy($id)
    {
        try {
            Application::destroy($id);

            return redirect()->back()->with('message', 'Application Removed Successfully');
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
