@extends('components.layouts.web')

@section('content')

    <div class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100 flex items-center justify-center min-h-screen">
    <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 mx-auto mb-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m-6 0V8a4 4 0 014-4h8a4 4 0 014 4v12a4 4 0 01-4 4H8a4 4 0 01-4-4v-8a4 4 0 014-4h8a4 4 0 014 4v4m0 0h4v4h-4v-4z" />
        </svg>
        <h1 class="text-2xl font-semibold mb-4">You are offline</h1>
        <p class="mb-4">It looks like you are not connected to the internet. Please check your connection and try again.</p>
        <a href="/" class="text-blue-500 hover:underline">Go back to the homepage</a>
    </div>
    </div>
@endsection
