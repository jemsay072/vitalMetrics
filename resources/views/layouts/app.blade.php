<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VitalMetrics') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-[#1b1b18] min-h-screen">
        <x-alert-text />
        <div class="min-h-screen bg-[#ffecec] flex w-full relative transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0"
             x-data="sidebarManager()">
            <!-- Mobile sidebar backdrop -->
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-10 bg-gray-600 bg-opacity-75 lg:hidden"
                 x-on:click="sidebarOpen = false">
            </div>

            <aside class="sidebar flex flex-col justify-between w-[265px] bg-[#353030] py-2 fixed h-full z-20 transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
                   :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
                <x-side-bar />
            </aside >

            <!-- Page Content -->
            <main class="flex w-full flex-col lg:ml-[265px]">
                <div class="sticky top-0 z-10 flex w-full bg-[#353030] h-[4rem] text-white justify-between items-center px-5">
                    <!-- Mobile menu button -->
                    <button x-on:click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden text-white hover:text-gray-300 focus:outline-none focus:text-gray-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="sidebarOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Desktop navigation -->
                    <div class="flex-1 lg:flex lg:justify-end">
                        @include('layouts.navigation')
                    </div>
                </div>
                <!-- Content -->
                <div class="main-content p-4 sm:p-6">
                    <div class="flex flex-row items-center gap-4">
                        <!-- Page Heading -->
                        @isset($header)
                            <header class="w-full">
                                <div class="max-w-7xl w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                    {{ $header }}
                                </div>
                            </header>
                        @endisset
                    </div>
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
