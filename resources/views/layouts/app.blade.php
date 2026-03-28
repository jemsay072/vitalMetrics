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
        <div class="min-h-screen bg-[#ffecec] flex w-full min-h-screen relative transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <aside class="sidebar flex flex-col justify-between w-[265px] bg-[#353030] py-2 fixed h-full z-20">
                <x-side-bar />
            </aside >

            <!-- Page Content -->
            <main class="flex w-full flex-col">
                <div class="sticky top-0 z-10 flex w-full bg-[#353030] h-[4rem] text-white justify-end items-center px-5">
                    @include('layouts.navigation')
                </div>
                <!-- Content -->
                <div class="main-content p-6 md:pl-[265px]">
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
