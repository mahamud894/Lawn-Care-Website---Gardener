<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Forgot Password - Gardener') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    @include('sweetalert::alert')
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('Forgot Password?') }}</h1>
            <p class="text-gray-600">{{ __('Enter your e-mail and we\'ll send you a reset link.') }}</p>
        </div>

        <form action="{{ route('password.email') }}" method="post" class="space-y-6">
            @csrf
            @include('common.auth.errors')
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email Address') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" placeholder="Enter your email">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium">{{ __('Send Reset Link') }}</button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium">{{ __('Back to login') }}</a>
        </div>
    </div>
</body>
</html>
