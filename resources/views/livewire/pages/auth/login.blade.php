<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\form;
use function Livewire\Volt\layout;

layout('components.layouts.web');

form(LoginForm::class);
$login = function () {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    $this->redirectIntended(default: route('web.home', absolute: false), navigate: true);
};

?>

<div class="" x-data>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')"/>

    <form wire:submit.prevent="login" class="bg-white dark:bg-darker shadow-lg rounded-lg p-6 max-w-md mx-auto space-y-4">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300"/>
            <input wire:model="form.email" id="email" class="block mt-1 dark:text-gray-200 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm px-4 py-2 bg-gray-100 dark:bg-darkBg  focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-darkBg " type="email" name="email" required autofocus autocomplete="username"/>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-red-600 dark:text-red-400"/>
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-300"/>
            <input wire:model="form.password" id="password" class="block mt-1 dark:text-gray-200 w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm px-4 py-2 bg-gray-100 dark:bg-darkBg  focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-darkBg " type="password" name="password" required autocomplete="current-password"/>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-red-600 dark:text-red-400"/>
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center text-gray-700 dark:text-gray-300">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-darkBg ">
                <span class="ml-2 text-sm">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Links -->
        <div class="flex items-center justify-between mt-6 space-x-2">
            <div>
                @if (Route::has('password.request'))
                    <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors duration-200" href="{{ route('password.request') }}" wire:navigate>{{ __('Forgot your password?') }}</a>
                @endif
            </div>
            <div>
                <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-500 dark:hover:text-indigo-400 transition-colors duration-200" href="{{ route('register') }}" wire:navigate>{{ __('Create new account?') }}</a>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end mt-4">
            <x-primary-button class="px-6 py-2 text-base font-semibold bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg shadow-md transition-all duration-200 ms-3 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:focus:ring-indigo-600">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
        <div class="flex justify-between space-x-2 mt-6">
            <x-secondary-button @click="$wire.form.email='admin@mail.com', $wire.form.password='000000', $wire.login()" class="capitalize mx-auto my-2 text-base px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md shadow-md transition-all duration-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300">
                @lang('admin')
            </x-secondary-button>
            <x-secondary-button @click="$wire.form.email='user@mail.com', $wire.form.password='000000', $wire.login()" class="capitalize mx-auto my-2 text-base px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md shadow-md transition-all duration-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300">
                @lang('user')
            </x-secondary-button>
        </div>

        <!-- Social Login -->
        <div class="flex flex-col space-y-3 mt-6">
            <a href="{{ route('socialite.auth', 'google') }}" class="flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200">
                <i class='bx bxl-google bx-tada text-2xl text-gray-500 dark:text-gray-300'></i>
                <span class="ml-2 text-gray-600 dark:text-gray-300">@lang('Login with Google')</span>
            </a>
            <a href="{{ route('socialite.auth', 'github') }}" class="flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-800 dark:text-gray-300 border border-gray-300 dark:border-gray-700 rounded-md shadow-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200">
                <i class='bx bxl-github bx-tada text-2xl text-gray-500 dark:text-gray-300'></i>
                <span class="ml-2 text-gray-600 dark:text-gray-300">@lang('Login with GitHub')</span>
            </a>
        </div>
    </form>

    <!-- Quick Login for Admin and User -->




</div>
