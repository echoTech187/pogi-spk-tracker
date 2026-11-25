<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Tampilkan agenda acara ilmiah & kategori SKP secara live dari basis data MySQL.
     */
    public function index(Request $request): View
    {
        $rawEvents = DB::table('skp_events')
            ->leftJoin('event_categories', 'skp_events.category_id', '=', 'event_categories.id')
            ->select(
                'skp_events.*',
                'event_categories.name as category_name',
                'event_categories.domain as category_domain'
            )
            ->orderBy('skp_events.event_date', 'desc')
            ->get();

        $eventCompletions = DB::table('skp_entries')
            ->select('event_id', DB::raw('COUNT(DISTINCT doctor_id) as done_count'))
            ->whereNotNull('event_id')
            ->groupBy('event_id')
            ->pluck('done_count', 'event_id');

        $events = $rawEvents->map(function ($e) use ($eventCompletions) {
            $domain = ucfirst(strtolower($e->category_domain ?: 'Pembelajaran'));
            $doneCount = (int) ($eventCompletions[$e->id] ?? 0);
            return [
                'id' => (string) $e->id,
                'title' => $e->title,
                'type' => $domain,
                'category' => $e->category_name ?: 'Simposium',
                'credits' => (float) $e->credit_value,
                'creditsDisplay' => (string) $e->credit_value,
                'date' => $e->event_date ? Carbon::parse($e->event_date)->format('d M Y') : '-',
                'venue' => $e->organizer ?: 'PP POGI / Daring',
                'doctors' => "{$doneCount} selesai",
            ];
        })->all();

        $categories = DB::table('event_categories')
            ->select('event_categories.*')
            ->selectRaw('(SELECT COUNT(*) FROM skp_events WHERE skp_events.category_id = event_categories.id) as events_count')
            ->orderBy('name')
            ->get();

        $totalEvents = $rawEvents->count();
        $totalSkpCredits = $rawEvents->sum('credit_value');
        $now = Carbon::now()->toDateString();
        $upcomingEvents = $rawEvents->where('event_date', '>=', $now)->count();

        $metrics = [
            'total_events' => $totalEvents,
            'total_credits' => round($totalSkpCredits, 1),
            'upcoming_events' => $upcomingEvents,
            'total_categories' => $categories->count(),
        ];

        $doctors = DB::table('doctors')
            ->leftJoin('pogi_branches', 'doctors.branch_id', '=', 'pogi_branches.id')
            ->leftJoin('institutions', 'doctors.primary_institution_id', '=', 'institutions.id')
            ->leftJoin('provinces', 'doctors.province_id', '=', 'provinces.id')
            ->select(
                'doctors.id',
                'doctors.full_name as name',
                'doctors.npa_pogi as pogiId',
                'provinces.name as province',
                'institutions.name as institution'
            )
            ->take(15)
            ->get();

        return view('events', [
            'events' => $events,
            'categories' => $categories,
            'metrics' => $metrics,
            'doctors' => $doctors,
        ]);
    }
}
