<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/** Business numbers for the admin: bookings, revenue, top services and gardeners. */
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $months = (int) $request->query('months', 12);
        $months = in_array($months, [3, 6, 12, 24]) ? $months : 12;

        $from = now()->startOfMonth()->subMonths($months - 1);

        // Grouping in PHP keeps this working on both MySQL and SQLite
        $bookings = Booking::with(['service', 'teamMember'])
            ->where('created_at', '>=', $from)
            ->get();

        $labels = [];
        $bookingCounts = [];
        $revenue = [];

        for ($m = $from->copy(); $m->lte(now()); $m->addMonth()) {
            $key = $m->format('Y-m');
            $labels[] = $m->format('M Y');
            $inMonth = $bookings->filter(fn ($b) => $b->created_at->format('Y-m') === $key);
            $bookingCounts[] = $inMonth->count();
            $revenue[] = round($bookings
                ->filter(fn ($b) => $b->isPaid() && $b->paid_at && $b->paid_at->format('Y-m') === $key)
                ->sum(fn ($b) => (float) $b->amount_paid), 2);
        }

        $statusBreakdown = collect(Booking::STATUSES)
            ->mapWithKeys(fn ($s) => [$s => $bookings->where('status', $s)->count()]);

        $topServices = $bookings->groupBy(fn ($b) => $b->service?->title ?? 'No service')
            ->map(fn ($group, $title) => [
                'title' => $title,
                'count' => $group->count(),
                'revenue' => $group->filter->isPaid()->sum(fn ($b) => (float) $b->amount_paid),
            ])
            ->sortByDesc('count')->values()->take(8);

        $topGardeners = $bookings->where('status', 'done')->whereNotNull('team_member_id')
            ->groupBy(fn ($b) => $b->teamMember?->title ?? 'Unknown')
            ->map(fn ($group, $name) => ['name' => $name, 'jobs' => $group->count()])
            ->sortByDesc('jobs')->values()->take(8);

        $totals = [
            'bookings' => $bookings->count(),
            'revenue' => array_sum($revenue),
            'unpaid' => $bookings->where('status', 'done')->reject->isPaid()->sum(fn ($b) => $b->amountDue()),
            'completion' => $bookings->count()
                ? round($bookings->where('status', 'done')->count() / max(1, $bookings->whereNotIn('status', ['pending'])->count()) * 100)
                : 0,
        ];

        return view('pages.admin-panel.reports', compact(
            'months', 'labels', 'bookingCounts', 'revenue', 'statusBreakdown', 'topServices', 'topGardeners', 'totals'
        ));
    }
}
