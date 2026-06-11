<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\SiteVisit;
use App\Models\Quotation;
use App\Models\ExperienceCenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Base queries scoped to user
        $leadQuery = Lead::forUser($user);
        $siteVisitQuery = SiteVisit::forUser($user);
        $oppQuery = Opportunity::forUser($user);
        $quotationQuery = Quotation::forUser($user);

        $stats = [
            'total_leads'     => (clone $leadQuery)->count(),
            'new_leads'       => (clone $leadQuery)->where('status', 'new')->count(),
            'qualified_leads' => (clone $leadQuery)->where('status', 'qualified')->count(),
            'won_leads'       => (clone $leadQuery)->where('status', 'won')->count(),
            'lost_leads'      => (clone $leadQuery)->where('status', 'lost')->count(),
            'site_visits'     => $siteVisitQuery->count(),
            'open_opps'       => (clone $oppQuery)->whereNotIn('stage', ['won', 'lost'])->count(),
            'quotation_value' => $quotationQuery->sum('quotation_value'),
            'won_value'       => (clone $oppQuery)->where('stage', 'won')->sum('expected_revenue'),
            'lost_value'      => (clone $oppQuery)->where('stage', 'lost')->sum('expected_revenue'),
        ];

        // Top lead sources
        $topSources = (clone $leadQuery)
            ->select('lead_source', DB::raw('count(*) as count'))
            ->whereNotNull('lead_source')
            ->groupBy('lead_source')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top experience centers (admin/manager only)
        $topCenters = [];
        if ($user->isSuperAdmin() || $user->isSalesManager()) {
            $topCenters = ExperienceCenter::withCount('leads')
                ->orderByDesc('leads_count')
                ->limit(5)
                ->get();
        }

        // Recent leads
        $recentLeads = (clone $leadQuery)
            ->with('assignedTo', 'experienceCenter')
            ->latest()
            ->limit(10)
            ->get();

        // Pending follow-ups (due today or overdue)
        $pendingFollowups = (clone $leadQuery)
            ->whereNotNull('next_followup_date')
            ->whereDate('next_followup_date', '<=', today())
            ->whereNotIn('status', ['won', 'lost'])
            ->count();

        // Pipeline by stage
        $pipeline = (clone $leadQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('dashboard.index', compact(
            'stats', 'topSources', 'topCenters', 'recentLeads', 'pendingFollowups', 'pipeline'
        ));
    }
}
