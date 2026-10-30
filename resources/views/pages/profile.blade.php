@extends('layouts.site')

@section('title', __('My Profile'))
@section('header', __('My Profile'))

@section('content')
<div class="container-xxl py-5">
    <div class="container" style="max-width: 900px;">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-md-6">
                <div class="bg-light rounded p-4 h-100">
                    <h4 class="mb-4">{{ __('Personal details') }}</h4>
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="pname">{{ __('Full Name') }}</label>
                            <input type="text" id="pname" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="pemail">{{ __('Email Address') }}</label>
                            <input type="email" id="pemail" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="pphone">{{ __('Phone Number') }}</label>
                            <input type="tel" id="pphone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="01712345678">
                            <small class="text-muted">{{ __('We send booking updates by SMS to this number.') }}</small>
                        </div>
                        <button type="submit" class="btn btn-primary py-2 px-4">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="bg-light rounded p-4 h-100">
                    <h4 class="mb-4">{{ __('Change password') }}</h4>
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @if(! $user->google_id)
                            <div class="mb-3">
                                <label class="form-label" for="cpw">{{ __('Current password') }}</label>
                                <input type="password" id="cpw" name="current_password" class="form-control" required autocomplete="current-password">
                            </div>
                        @else
                            <p class="small text-muted">{{ __('You signed up with Google, so you can set a password here to also log in with your e-mail.') }}</p>
                        @endif
                        <div class="mb-3">
                            <label class="form-label" for="npw">{{ __('New password') }}</label>
                            <input type="password" id="npw" name="password" class="form-control" minlength="8" required autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="npw2">{{ __('Confirm new password') }}</label>
                            <input type="password" id="npw2" name="password_confirmation" class="form-control" minlength="8" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-dark py-2 px-4">{{ __('Change password') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
