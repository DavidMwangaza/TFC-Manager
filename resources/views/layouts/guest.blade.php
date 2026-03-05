<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900">
            <div class="mb-6 text-center">
                <a href="/" class="inline-block">
                    <div class="bg-white rounded-full p-3 shadow-lg mx-auto" style="width: 90px; height: 90px;">
                        <x-application-logo class="w-full h-full object-contain rounded-full" />
                    </div>
                </a>
                <h1 class="mt-3 text-xl font-bold text-white">UDBL TFC Manager</h1>
                <p class="text-blue-200 text-sm">Universit&eacute; Don Bosco de Lubumbashi</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-xl overflow-hidden sm:rounded-xl">
                {{ $slot }}
            </div>

            <p class="mt-6 text-blue-300 text-xs">&copy; {{ date('Y') }} UDBL &mdash; Tous droits r&eacute;serv&eacute;s</p>
        </div>
    </body>
</html>
