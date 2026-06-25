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
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600|lora:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-zinc-800 antialiased relative overflow-hidden bg-zinc-50">
        <!-- Abstract glowing circles matching welcome page -->
        <div class="absolute -right-24 -top-24 w-96 h-96 bg-accent/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-24 -bottom-24 w-96 h-96 bg-primary-light/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-slate-900 via-primary-dark to-slate-950 relative z-10">
            <div class="mb-6 text-center animate-fade-in-up">
                <a href="/" class="inline-block group">
                    <div class="bg-white/10 backdrop-blur-md rounded-3xl p-3 border border-white/15 shadow-2xl mx-auto transition-transform duration-500 group-hover:scale-105" style="width: 100px; height: 100px;">
                        <div class="bg-white w-full h-full rounded-2xl p-2 shadow-inner">
                            <x-application-logo class="w-full h-full object-contain" />
                        </div>
                    </div>
                </a>
                <h1 class="mt-4 text-2xl font-serif font-black text-white tracking-tight leading-relaxed">TFC Manager</h1>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mt-1">Université Don Bosco de Lubumbashi</p>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white/95 backdrop-blur-md shadow-2xl overflow-hidden sm:rounded-2xl border border-white/20">
                {{ $slot }}
            </div>

            <p class="mt-8 text-slate-500 text-[10px] font-semibold uppercase tracking-wider">&copy; {{ date('Y') }} UDBL &mdash; Tous droits r&eacute;serv&eacute;s</p>
        </div>
    </body>
</html>
