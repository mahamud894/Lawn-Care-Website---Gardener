{{--
    Booking form used on the Home and Quote pages.
    Needs $services; $selectedServiceId is optional.
    Its JavaScript lives in common.site.booking-form-script (include it after jQuery).
--}}
@php
    $selectedServiceId = $selectedServiceId ?? null;
    $user = Auth::user();
@endphp

@if ($errors->any())
<div class="alert alert-danger text-start">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form class="booking-form" action="{{ route('createBooking') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="row g-3">

        {{-- Booking for myself / someone else --}}
        <div class="col-12 mb-2 text-start">
            <label class="form-label fw-bold text-dark">{{ __('Booking For:') }}</label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input booking-for" type="radio" name="booking_for" id="{{ $formId }}ForMyself" value="myself" checked>
                    <label class="form-check-label text-dark" for="{{ $formId }}ForMyself">{{ __('For Myself') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input booking-for" type="radio" name="booking_for" id="{{ $formId }}ForOthers" value="others">
                    <label class="form-check-label text-dark" for="{{ $formId }}ForOthers">{{ __('For Someone Else') }}</label>
                </div>
            </div>
        </div>

        <div class="col-12 text-start">
            <input name="name" value="{{ old('name', $user->name ?? '') }}" data-mine="{{ $user->name ?? '' }}" type="text" class="form-control bg-light border-0 bf-name" placeholder="{{ __('Your Name') }}" style="height: 55px;" minlength="3" required>
        </div>

        <div class="col-md-6">
            <input name="email" value="{{ old('email', $user->email ?? '') }}" data-mine="{{ $user->email ?? '' }}" type="email" class="form-control bg-light border-0 bf-email" placeholder="{{ __('Your Email') }}" style="height: 55px;" required>
        </div>
        <div class="col-md-6">
            <input name="phone" value="{{ old('phone', $user->phone ?? '') }}" data-mine="{{ $user->phone ?? '' }}" type="tel" class="form-control bg-light border-0 bf-phone" placeholder="{{ __('Phone (e.g. 01712345678)') }}" style="height: 55px;" required>
        </div>

        <div class="col-12">
            <input type="text" name="address" value="{{ old('address') }}" class="form-control border-0 bg-light px-4" placeholder="{{ __('Your Address') }}" style="height: 55px;" required>
        </div>

        <div class="col-md-7">
            <select name="service" class="form-select bg-light border-0 bf-service" style="height: 55px;" required>
                <option value="" disabled {{ old('service', $selectedServiceId) ? '' : 'selected' }}>{{ __('Select A Service') }}</option>
                @foreach($services as $service)
                <option value="{{ $service->id }}"
                        data-price="{{ (float) $service->price }}"
                        data-per100="{{ (float) $service->price_per_100_sqft }}"
                        {{ old('service', $selectedServiceId) == $service->id ? 'selected' : '' }}>
                    {{ $service->title }}{{ $service->price ? ' ('.money($service->price).')' : '' }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <input type="number" name="lawn_size" min="50" step="10" value="{{ old('lawn_size') }}" class="form-control bg-light border-0 bf-size" placeholder="{{ __('Lawn size (sq ft)') }}" style="height: 55px;">
        </div>

        {{-- Live price estimate --}}
        <div class="col-12 bf-estimate-box" style="display: none;">
            <div class="rounded p-3 text-start" style="background: #eaf5ea;">
                <span class="text-muted">{{ __('Estimated price') }}:</span>
                <strong class="fs-5 text-primary bf-estimate"></strong>
                <small class="d-block text-muted bf-estimate-note"></small>
            </div>
        </div>

        <div class="col-md-6">
            <input name="preferred_date" type="date" min="{{ now()->addDay()->toDateString() }}" value="{{ old('preferred_date') }}" class="form-control bg-light border-0 bf-date" style="height: 55px;" required>
        </div>
        <div class="col-md-6">
            <select name="time_slot" class="form-control bg-light border-0 bf-slot" data-old="{{ old('time_slot') }}" style="height: 55px; color: #6c757d; background-color: #f8f9fa !important;" required disabled>
                <option value="">{{ __('First select a date') }}</option>
            </select>
        </div>

        <div class="col-12">
            <select name="recurrence" class="form-select bg-light border-0" style="height: 55px;">
                @foreach(\App\Models\Booking::RECURRENCES as $value => $label)
                    <option value="{{ $value }}" @selected(old('recurrence', 'none') === $value)>{{ __('Repeat') }}: {{ __($label) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12">
            <textarea name="message" class="form-control bg-light border-0" rows="3" placeholder="{{ __('Message') }}">{{ old('message') }}</textarea>
        </div>

        <div class="col-12 text-start">
            <label class="form-label small text-muted mb-1"><i class="fa fa-camera me-1"></i>{{ __('Photos of your lawn (optional, up to 3)') }}</label>
            <input type="file" name="photos[]" class="form-control bg-light border-0" accept="image/*" multiple>
        </div>

        <div class="col-12">
            @auth
            <button class="btn btn-dark w-100 py-3" type="submit">{{ __('Submit') }}</button>
            @else
            <a href="{{ route('login') }}" class="btn btn-dark w-100 py-3">{{ __('Login to Book') }}</a>
            @endauth
        </div>
    </div>
</form>
