<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ $booking->invoiceNumber() }} - {{ config('gardener.business_name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('site/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body { background: #eef2ee; font-size: 14px; }
        .invoice { max-width: 800px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,.06); }
        .brand { color: #348E38; font-weight: 800; font-size: 28px; }
        .paid-stamp { border: 3px solid #198754; color: #198754; font-weight: 800; padding: 4px 14px; border-radius: 6px; transform: rotate(-6deg); display: inline-block; }
        .unpaid-stamp { border: 3px solid #dc3545; color: #dc3545; font-weight: 800; padding: 4px 14px; border-radius: 6px; display: inline-block; }
        @media print {
            body { background: #fff; }
            .invoice { box-shadow: none; margin: 0; max-width: none; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="text-center mt-3 no-print">
        <button onclick="window.print()" class="btn btn-success">{{ __('Print / Save as PDF') }}</button>
        <a href="{{ url()->previous() }}" class="btn btn-light">{{ __('Back') }}</a>
    </div>

    <div class="invoice">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <div class="brand">🌿 {{ config('gardener.business_name') }}</div>
                <div class="text-muted small">192 Mirzapur, Kalia, Narail<br>+880 1846650807 · mdmahamudul894@gmail.com</div>
            </div>
            <div class="text-end">
                <h4 class="mb-1">{{ __('INVOICE') }}</h4>
                <div class="small">{{ $booking->invoiceNumber() }}</div>
                <div class="small text-muted">{{ __('Date') }}: {{ ($booking->paid_at ?? $booking->created_at)->format('d M Y') }}</div>
                <div class="mt-2">
                    @if($booking->isPaid())
                        <span class="paid-stamp">{{ __('PAID') }}</span>
                    @else
                        <span class="unpaid-stamp">{{ __('UNPAID') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <div class="text-muted small text-uppercase fw-bold">{{ __('Bill to') }}</div>
                <div class="fw-bold">{{ $booking->name }}</div>
                <div>{{ $booking->email }}</div>
                @if($booking->phone)<div>{{ $booking->phone }}</div>@endif
                <div>{{ $booking->address }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="text-muted small text-uppercase fw-bold">{{ __('Service date') }}</div>
                <div>{{ $booking->preferred_date ? \Illuminate\Support\Carbon::parse($booking->preferred_date)->format('d M Y') : '-' }}</div>
                <div>{{ $booking->time_slot }}</div>
                @if($booking->teamMember)<div class="small text-muted">{{ __('Gardener') }}: {{ $booking->teamMember->title }}</div>@endif
            </div>
        </div>

        <table class="table">
            <thead class="table-light">
                <tr><th>{{ __('Description') }}</th><th class="text-end">{{ __('Amount') }}</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $booking->service?->title ?? __('Gardening service') }}
                        @if($booking->lawn_size)<div class="small text-muted">{{ __('Lawn size') }}: {{ number_format($booking->lawn_size) }} sq ft</div>@endif
                        <div class="small text-muted">{{ __('Booking') }} #{{ $booking->id }}</div>
                    </td>
                    <td class="text-end">{{ money($booking->amountDue()) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr><th class="text-end">{{ __('Total') }}</th><th class="text-end">{{ money($booking->amountDue()) }}</th></tr>
                @if($booking->isPaid())
                    <tr><td class="text-end text-muted">{{ __('Paid') }} ({{ $booking->payment_method }}, {{ $booking->paid_at?->format('d M Y') }})</td><td class="text-end text-success">− {{ money($booking->amount_paid) }}</td></tr>
                    <tr><th class="text-end">{{ __('Balance due') }}</th><th class="text-end">{{ money(max(0, $booking->amountDue() - (float) $booking->amount_paid)) }}</th></tr>
                @endif
            </tfoot>
        </table>

        @foreach($booking->payments as $p)
            <div class="small text-muted">{{ __('Transaction') }}: {{ $p->tran_id }} · {{ $p->method }}</div>
        @endforeach

        <p class="text-center text-muted small mt-5 mb-0">{{ __('Thank you for choosing us to care for your garden!') }}</p>
    </div>
</body>
</html>
