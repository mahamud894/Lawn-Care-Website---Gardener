@extends('admin-panel')
@section("content")
<div class="mx-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h5 class="mb-1">Business Reports</h5>
            <p class="text-muted small mb-0">Bookings are counted by the day they were made; revenue by the day it was paid.</p>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <label class="small text-muted">Period</label>
            <select name="months" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach([3, 6, 12, 24] as $m)
                    <option value="{{ $m }}" @selected($months === $m)>Last {{ $m }} months</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Bookings', $totals['bookings'], 'bi-journal-check', 'success'],
            ['Revenue collected', money($totals['revenue']), 'bi-cash-stack', 'primary'],
            ['Unpaid (finished jobs)', money($totals['unpaid']), 'bi-exclamation-circle', 'danger'],
            ['Completion rate', $totals['completion'].'%', 'bi-graph-up', 'info'],
        ] as [$label, $value, $icon, $color])
            <div class="col-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small text-uppercase fw-bold">{{ $label }}</div>
                            <div class="fs-4 fw-bold">{{ $value }}</div>
                        </div>
                        <i class="bi {{ $icon }} fs-2 text-{{ $color }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <h6 class="fw-bold">Bookings & revenue per month</h6>
                <div style="position: relative; height: 320px;"><canvas id="monthlyChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <h6 class="fw-bold">Bookings by status</h6>
                <div style="position: relative; height: 320px;"><canvas id="statusChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <h6 class="fw-bold">Most booked services</h6>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Service</th><th class="text-end">Bookings</th><th class="text-end">Revenue</th></tr></thead>
                    <tbody>
                        @forelse($topServices as $row)
                            <tr><td>{{ $row['title'] }}</td><td class="text-end">{{ $row['count'] }}</td><td class="text-end">{{ money($row['revenue']) }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <h6 class="fw-bold">Jobs completed per gardener</h6>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Gardener</th><th class="text-end">Jobs done</th></tr></thead>
                    <tbody>
                        @forelse($topGardeners as $row)
                            <tr><td>{{ $row['name'] }}</td><td class="text-end">{{ $row['jobs'] }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No data yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Same numbers as a table (also shown if the chart library can't load) --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mt-3">
        <h6 class="fw-bold">Monthly figures</h6>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>Month</th>@foreach($labels as $l)<th class="text-end">{{ $l }}</th>@endforeach</tr></thead>
                <tbody>
                    <tr><td>Bookings</td>@foreach($bookingCounts as $v)<td class="text-end">{{ $v }}</td>@endforeach</tr>
                    <tr><td>Revenue</td>@foreach($revenue as $v)<td class="text-end">{{ number_format($v) }}</td>@endforeach</tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    if (window.Chart) {
        new Chart(document.getElementById('monthlyChart'), {
            data: {
                labels: @json($labels),
                datasets: [
                    { type: 'bar', label: 'Bookings', data: @json($bookingCounts), backgroundColor: '#348E38', yAxisID: 'y' },
                    { type: 'line', label: 'Revenue ({{ config('gardener.currency_symbol') }})', data: @json($revenue), borderColor: '#0d6efd', backgroundColor: '#0d6efd', tension: .3, yAxisID: 'y1' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Bookings' } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Revenue' } }
                }
            }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: @json($statusBreakdown->keys()->map(fn ($s) => ucfirst($s))),
                datasets: [{ data: @json($statusBreakdown->values()), backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545', '#6c757d'] }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }
</script>
@endsection
