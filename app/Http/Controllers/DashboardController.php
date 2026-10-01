<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\RibiClub;
use App\Models\RibiCyeo;
use App\Models\RibiDyeo;
use App\Models\SentEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $roles = $user ? $user->getRoleNames()->toArray() : [];
        $role = $roles[0] ?? 'guest';

        $stats = [
            'total_applications' => 0,
            'camps_count' => 0,
            'step_count' => 0,
            'pending_count' => 0,
            'approved_count' => 0,
            'fee_paid_count' => 0,
            'total_clubs' => 0,
            'total_dyeos' => 0,
            'total_cyeos' => 0,
            'total_users' => 0,
        ];

        $statusBreakdown = [];
        $exchangeTypeBreakdown = [];
        $recentApplications = [];
        $recentEmails = [];
        $applicantApplication = null;

        if ($user && $user->hasRole('admin')) {
            $stats['total_applications'] = Application::count();
            $stats['camps_count'] = Application::where('exchange_type', 'CAMPS & TOURS')->count();
            $stats['step_count'] = Application::where('exchange_type', '!=', 'CAMPS & TOURS')->count();
            $stats['fee_paid_count'] = Application::where('application_fee_paid', true)->orWhere('application_fee_paid', 1)->count();
            $stats['total_clubs'] = RibiClub::count();
            $stats['total_dyeos'] = RibiDyeo::count();
            $stats['total_cyeos'] = RibiCyeo::count();
            $stats['total_users'] = User::count();

            $statusBreakdown = Application::selectRaw('COALESCE(NULLIF(application_status, ""), "Unassigned") as status, count(*) as count')
                ->groupBy('status')
                ->orderByDesc('count')
                ->pluck('count', 'status')
                ->toArray();

            $exchangeTypeBreakdown = Application::selectRaw('COALESCE(NULLIF(exchange_type, ""), "Other") as type, count(*) as count')
                ->groupBy('type')
                ->orderByDesc('count')
                ->pluck('count', 'type')
                ->toArray();

            $recentApplications = Application::with(['club', 'dyeo'])
                ->orderByDesc('id')
                ->take(8)
                ->get()
                ->map(fn ($app) => [
                    'id' => $app->id,
                    'application_no' => $app->application_no,
                    'full_name' => $app->full_name,
                    'email_address' => $app->email_address,
                    'exchange_type' => $app->exchange_type,
                    'application_status' => $app->application_status,
                    'date_of_app' => $app->date_of_app,
                    'club_name' => $app->club?->club_name ?? '-',
                    'district_code' => $app->dyeo?->district_code ?? $app->club?->district_code ?? '-',
                ]);

            $recentEmails = SentEmail::orderByDesc('id')
                ->take(5)
                ->get()
                ->map(fn ($email) => [
                    'id' => $email->id,
                    'recipient' => $email->recipient ?? $email->to ?? '-',
                    'subject' => $email->subject ?? 'Email Notification',
                    'sent_at' => $email->created_at?->format('d M Y, H:i') ?? '-',
                ]);
        } elseif ($user && $user->hasRole('dyeo')) {
            $dyeo = RibiDyeo::where('user_id', $user->id)->first();
            $query = Application::query();
            if ($dyeo) {
                $query->where('dyeo_id', $dyeo->id);
            }

            $stats['total_applications'] = (clone $query)->count();
            $stats['camps_count'] = (clone $query)->where('exchange_type', 'CAMPS & TOURS')->count();
            $stats['step_count'] = (clone $query)->where('exchange_type', '!=', 'CAMPS & TOURS')->count();
            $stats['fee_paid_count'] = (clone $query)->where(function ($q) {
                $q->where('application_fee_paid', true)->orWhere('application_fee_paid', 1);
            })->count();

            if ($dyeo && $dyeo->district_code) {
                $stats['total_clubs'] = RibiClub::where('district_code', $dyeo->district_code)->count();
            } else {
                $stats['total_clubs'] = RibiClub::count();
            }

            $statusBreakdown = (clone $query)
                ->selectRaw('COALESCE(NULLIF(application_status, ""), "Unassigned") as status, count(*) as count')
                ->groupBy('status')
                ->orderByDesc('count')
                ->pluck('count', 'status')
                ->toArray();

            $exchangeTypeBreakdown = (clone $query)
                ->selectRaw('COALESCE(NULLIF(exchange_type, ""), "Other") as type, count(*) as count')
                ->groupBy('type')
                ->orderByDesc('count')
                ->pluck('count', 'type')
                ->toArray();

            $recentApplications = (clone $query)->with(['club', 'dyeo'])
                ->orderByDesc('id')
                ->take(8)
                ->get()
                ->map(fn ($app) => [
                    'id' => $app->id,
                    'application_no' => $app->application_no,
                    'full_name' => $app->full_name,
                    'email_address' => $app->email_address,
                    'exchange_type' => $app->exchange_type,
                    'application_status' => $app->application_status,
                    'date_of_app' => $app->date_of_app,
                    'club_name' => $app->club?->club_name ?? '-',
                    'district_code' => $app->dyeo?->district_code ?? '-',
                ]);
        } elseif ($user && $user->hasRole('cyeo')) {
            $user->load('cyeo.club');
            $query = Application::query();
            if ($user->cyeo && $user->cyeo->ribi_club_id) {
                $query->where('rotary_club_id', $user->cyeo->ribi_club_id);
            }

            $stats['total_applications'] = (clone $query)->count();
            $stats['camps_count'] = (clone $query)->where('exchange_type', 'CAMPS & TOURS')->count();
            $stats['step_count'] = (clone $query)->where('exchange_type', '!=', 'CAMPS & TOURS')->count();
            $stats['fee_paid_count'] = (clone $query)->where(function ($q) {
                $q->where('application_fee_paid', true)->orWhere('application_fee_paid', 1);
            })->count();
            $stats['total_clubs'] = 1;

            $statusBreakdown = (clone $query)
                ->selectRaw('COALESCE(NULLIF(application_status, ""), "Unassigned") as status, count(*) as count')
                ->groupBy('status')
                ->orderByDesc('count')
                ->pluck('count', 'status')
                ->toArray();

            $exchangeTypeBreakdown = (clone $query)
                ->selectRaw('COALESCE(NULLIF(exchange_type, ""), "Other") as type, count(*) as count')
                ->groupBy('type')
                ->orderByDesc('count')
                ->pluck('count', 'type')
                ->toArray();

            $recentApplications = (clone $query)->with(['club', 'dyeo'])
                ->orderByDesc('id')
                ->take(8)
                ->get()
                ->map(fn ($app) => [
                    'id' => $app->id,
                    'application_no' => $app->application_no,
                    'full_name' => $app->full_name,
                    'email_address' => $app->email_address,
                    'exchange_type' => $app->exchange_type,
                    'application_status' => $app->application_status,
                    'date_of_app' => $app->date_of_app,
                    'club_name' => $app->club?->club_name ?? '-',
                    'district_code' => $app->dyeo?->district_code ?? '-',
                ]);
        } elseif ($user && $user->hasRole('applicant')) {
            $app = null;
            if ($user->application_id) {
                $app = Application::with(['club', 'dyeo', 'languages', 'siblings', 'media_library'])->find($user->application_id);
            }

            if ($app) {
                $applicantApplication = [
                    'id' => $app->id,
                    'application_no' => $app->application_no,
                    'full_name' => $app->full_name,
                    'firstname' => $app->firstname,
                    'surname' => $app->surname,
                    'email_address' => $app->email_address,
                    'exchange_type' => $app->exchange_type,
                    'application_status' => $app->application_status,
                    'application_status_note' => $app->application_status_note,
                    'application_fee_paid' => (bool) $app->application_fee_paid,
                    'date_of_app' => $app->date_of_app,
                    'club_name' => $app->club?->club_name ?? 'Not Assigned',
                    'dyeo_name' => $app->dyeo?->dyeo_name ?? 'Not Assigned',
                    'district_code' => $app->dyeo?->district_code ?? '-',
                    'media_count' => $app->media_library?->count() ?? 0,
                    'languages_count' => $app->languages?->count() ?? 0,
                ];
            }
        } else {
            // Default stats for users without specific roles
            $stats['total_applications'] = Application::count();
            $stats['total_clubs'] = RibiClub::count();
            $stats['total_dyeos'] = RibiDyeo::count();
            $stats['total_cyeos'] = RibiCyeo::count();
        }

        return Inertia::render('Dashboard', [
            'role' => $role,
            'stats' => $stats,
            'statusBreakdown' => $statusBreakdown,
            'exchangeTypeBreakdown' => $exchangeTypeBreakdown,
            'recentApplications' => $recentApplications,
            'recentEmails' => $recentEmails,
            'applicantApplication' => $applicantApplication,
            'userContext' => [
                'name' => $user?->full_name,
                'email' => $user?->email,
                'district' => $user?->district,
                'club_name' => $user?->club?->club_name,
            ],
        ]);
    }
}
