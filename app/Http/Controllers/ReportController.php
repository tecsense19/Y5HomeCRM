<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\ExperienceCenter;
use App\Models\SiteVisit;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function leadSource(Request $request)
    {
        $data = Lead::forUser(Auth::user())->select(
                'lead_source',
                DB::raw('count(*) as lead_count'),
                DB::raw("sum(case when status='won' then 1 else 0 end) as won_leads"),
                DB::raw("round(sum(case when status='won' then 1 else 0 end) * 100.0 / count(*), 1) as conversion_pct")
            )
            ->whereNotNull('lead_source')
            ->when($request->date_from, fn($q) => $q->whereDate('lead_creation_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('lead_creation_date', '<=', $request->date_to))
            ->when($request->status,    fn($q) => $q->where('status', $request->status))
            ->groupBy('lead_source')
            ->orderByDesc('lead_count')
            ->get();

        return view('reports.lead_source', compact('data'));
    }

    public function experienceCenter(Request $request)
    {
        $isCenterUser = Auth::user()->isExperienceCenterUser();
        $centerId = Auth::user()->experience_center_id;

        $centers = ExperienceCenter::orderBy('center_name')
            ->when($isCenterUser, fn($q) => $q->where('id', $centerId))
            ->get();

        $centersQuery = ExperienceCenter::withCount([
                'leads',
                'leads as won_leads_count' => fn($q) => $q->where('status', 'won'),
            ]);

        // Filter by specific center
        if ($isCenterUser) {
            $centersQuery->where('id', $centerId);
        } elseif ($request->filled('center_id')) {
            $centersQuery->where('id', $request->center_id);
        }

        $data = $centersQuery->get()->map(function ($center) use ($request) {
            $leadIds = $center->leads()
                ->when($request->date_from, fn($q) => $q->whereDate('lead_creation_date', '>=', $request->date_from))
                ->when($request->date_to,   fn($q) => $q->whereDate('lead_creation_date', '<=', $request->date_to))
                ->pluck('id');

            return [
                'center_name'    => $center->center_name,
                'leads_assigned' => $leadIds->count(),
                'site_visits'    => SiteVisit::whereIn('lead_id', $leadIds)->count(),
                'quotations'     => Quotation::whereHas('opportunity.lead', fn($q) => $q->whereIn('id', $leadIds))->count(),
                'won_projects'   => Lead::whereIn('id', $leadIds)->where('status', 'won')->count(),
                'revenue'        => Opportunity::where('stage', 'won')
                    ->whereHas('lead', fn($q) => $q->whereIn('id', $leadIds))
                    ->sum('expected_revenue'),
            ];
        });

        return view('reports.experience_center', compact('data', 'centers'));
    }

    public function salesPipeline(Request $request)
    {
        $query = Opportunity::forUser(Auth::user())->with('lead', 'assignedTo')
            ->when($request->stage,          fn($q) => $q->where('stage', $request->stage))
            ->when($request->customer,       fn($q) => $q->where('customer_name', 'like', '%' . $request->customer . '%'))
            ->when($request->min_value,      fn($q) => $q->where('expected_revenue', '>=', $request->min_value))
            ->when($request->max_value,      fn($q) => $q->where('expected_revenue', '<=', $request->max_value))
            ->when($request->closing_before, fn($q) => $q->whereDate('expected_closing_date', '<=', $request->closing_before));

        // Summary stats on full filtered result (before pagination)
        $allFiltered = (clone $query)->get();
        $stats = [
            'total_value' => $allFiltered->sum('expected_revenue'),
            'won_value'   => $allFiltered->where('stage', 'won')->sum('expected_revenue'),
            'avg_value'   => $allFiltered->count() > 0 ? $allFiltered->avg('expected_revenue') : 0,
            'won_count'   => $allFiltered->where('stage', 'won')->count(),
            'open_count'  => $allFiltered->whereNotIn('stage', ['won', 'lost'])->count(),
        ];

        $opportunities = $query->latest()->paginate(30)->withQueryString();

        return view('reports.sales_pipeline', compact('opportunities', 'stats'));
    }

    public function export(Request $request, string $type)
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$type}-report-" . now()->format('Y-m-d') . ".csv",
        ];

        $callback = match ($type) {
            'lead-source' => fn() => $this->exportLeadSource($request),
            'pipeline'    => fn() => $this->exportPipeline($request),
            default       => abort(404),
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportLeadSource(Request $request)
    {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Lead Source', 'Lead Count', 'Won Leads', 'Conversion %', 'Open Leads']);
        $rows = Lead::forUser(Auth::user())->select('lead_source', DB::raw('count(*) as c'), DB::raw("sum(case when status='won' then 1 else 0 end) as w"))
            ->whereNotNull('lead_source')
            ->when($request->date_from, fn($q) => $q->whereDate('lead_creation_date', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('lead_creation_date', '<=', $request->date_to))
            ->when($request->status,    fn($q) => $q->where('status', $request->status))
            ->groupBy('lead_source')
            ->get();
        foreach ($rows as $row) {
            $pct = $row->c > 0 ? round($row->w * 100 / $row->c, 1) : 0;
            fputcsv($out, [$row->lead_source, $row->c, $row->w, $pct, $row->c - $row->w]);
        }
        fclose($out);
    }

    private function exportPipeline(Request $request)
    {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Opportunity', 'Customer', 'Value (₹)', 'Stage', 'Assigned To', 'Expected Closing']);
        Opportunity::forUser(Auth::user())->with('assignedTo')
            ->when($request->stage,          fn($q) => $q->where('stage', $request->stage))
            ->when($request->customer,       fn($q) => $q->where('customer_name', 'like', '%' . $request->customer . '%'))
            ->when($request->min_value,      fn($q) => $q->where('expected_revenue', '>=', $request->min_value))
            ->when($request->max_value,      fn($q) => $q->where('expected_revenue', '<=', $request->max_value))
            ->when($request->closing_before, fn($q) => $q->whereDate('expected_closing_date', '<=', $request->closing_before))
            ->get()
            ->each(function ($o) use ($out) {
                fputcsv($out, [
                    $o->opportunity_number,
                    $o->customer_name,
                    $o->expected_revenue,
                    $o->stage,
                    $o->assignedTo?->name ?? '',
                    $o->expected_closing_date?->format('Y-m-d') ?? '',
                ]);
            });
        fclose($out);
    }
}
