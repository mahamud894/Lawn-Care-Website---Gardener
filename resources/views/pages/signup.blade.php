<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Sign Up - Gardener') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
@include('sweetalert::alert')
<div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ __('Create Account') }}</h1>
            <p class="text-gray-600">{{ __('Join us today') }}</p>
        </div>

        <form action="{{ route('signup') }}" method="post" class="space-y-6">
            @csrf
            @include('common.auth.errors')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Full Name') }}</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="Enter your full name"
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email Address') }}</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="Enter your email"
                >
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone Number') }}</label>
                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="Enter your phone"
                >
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Register As') }}</label>
                <select
                    name="role"
                    id="role"
                    required
                    class="w-full px-4 py-3 border border-gray-300 bg-white text-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    style="height: 50px;"
                >
                    <option value="0" @selected(old('role', '0') == '0')>{{ __('Regular User (Customer)') }}</option>
                    <option value="1" @selected(old('role') == '1')>{{ __('Admin Assistant (Requires Approval)') }}</option>
                </select>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Password') }}</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="At least 8 characters"
                    minlength="8"
                >
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Confirm Password') }}</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                    placeholder="Confirm your password"
                >
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 font-medium"
            >
                {{ __('Sign Up') }}
            </button>
        </form>

        @include('common.auth.google-button')

        <div class="mt-6 text-center">
            <p class="text-gray-600">{{ __('Already have an account?') }} <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium">{{ __('Sign in') }}</a></p>
        </div>
    </div>
</body>
</html>
