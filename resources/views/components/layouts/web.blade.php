<!DOCTYPE html>
<html lang="en" x-data="app()" x-bind:class="{ 'dark': darkMode }" class="antialiased">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Social Communication UI</title>
{{--    //get route name--}}
@php $route = \Illuminate\Support\Facades\Route::currentRouteName();@endphp
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        function app(){
            return {
                // Sidebar closed by default on small/medium, open on large+
                sidebarOpen: window.matchMedia('(min-width: 1024px)').matches,
                mobileSidebar: false,
                darkMode: localStorage.getItem('darkMode')==='true',
                active: @js($route),
                navItems: [
                    { id:'web.home', label:'Home', icon:'bx-home', href: '{{ route('web.home') }}' },
                    { id:'web.post.crud', label:'My Posts', icon:'bx-plus', href: '{{ route('web.post.crud') }}' },
                    { id:'web.category.wise.post', label:'Category Wise Post', icon:'bx-tag', href: '{{route('web.category.wise.post')}}' },
                    { id:'web.tag.wise.post', label:'Tag Wise Post', icon:'bx-tag-alt', href: '{{route('web.tag.wise.post')}}' },
                    { id:'web.user.wise.post', label:'User Wise Post', icon:'bx-user', href: '{{route('web.user.wise.post')}}' },
                ],

                toggleSidebar(){ this.sidebarOpen = !this.sidebarOpen },
                toggleDark(){ this.darkMode = !this.darkMode; localStorage.setItem('darkMode', this.darkMode) }
            }
        }
    </script>
    <style>[x-cloak]{display:none!important}</style>
    @laravelPWA
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>

</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 overflow-x-hidden">

<!-- Sidebar (fully collapsible) -->
@include('components.layouts.web-sidebar')
<!-- Overlay when sidebar open on small screens -->
<div x-show="sidebarOpen" @click="toggleSidebar()" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 lg:hidden" x-transition.opacity x-cloak></div>

<!-- Main wrapper adjusts padding only when sidebar visible on lg+ -->
<div class="min-h-screen flex flex-col " :class="sidebarOpen ? 'lg:pl-64' : 'lg:pl-0'">

    <!-- Header / Navbar (simplified, social style) -->
{{--@include('components.layouts.web-header')--}}
    <livewire:web.header-component/>
    @yield('content')

    @isset($slot)
        {{ $slot }}
    @endisset
    <!-- Composer -->
    </div>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-livewire-alert::scripts />
</body>
</html>
