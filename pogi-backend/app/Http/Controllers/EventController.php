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

        $events = $rawEvents->map(function ($e) {
            $domain = $e->category_domain ?: 'Pembelajaran';
            return [
                'id' => (string) $e->id,
                'title' => $e->title,
                'type' => $domain,
                'category' => $e->category_name ?: 'Simposium',
                'credits' => (float) $e->credit_value,
                'creditsDisplay' => (string) $e->credit_value,
                'date' => $e->event_date ? Carbon::parse($e->event_date)->format('d M Y') : '-',
                'venue' => $e->organizer ?: 'PP POGI / Daring',
                'doctors' => '98 / 100 selesai',
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

        return view('events', [
            'events' => $events,
            'categories' => $categories,
            'metrics' => $metrics,
        ]);
    }
}
