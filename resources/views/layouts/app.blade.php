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
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|lora:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-zinc-800 antialiased bg-zinc-50 custom-scrollbar relative">
        <!-- Abstract Glowing Background -->
        <div class="fixed top-0 inset-x-0 h-screen overflow-hidden pointer-events-none z-0">
            <div class="absolute -right-24 -top-24 w-96 h-96 bg-accent/5 rounded-full blur-3xl"></div>
            <div class="absolute -left-24 -bottom-24 w-96 h-96 bg-primary-light/5 rounded-full blur-3xl"></div>
        </div>
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar Navigation -->
            <x-sidebar-nav />

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto pt-16 lg:pt-0 relative">
                <!-- Topbar Header (Dynamic Page Title) -->
                @isset($header)
                    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/60 py-3 px-4 lg:px-6 shrink-0 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                        <div>
                            <h1 class="font-serif text-lg lg:text-xl font-bold text-slate-800 tracking-tight leading-relaxed">
                                {{ $header }}
                            </h1>
                        </div>
                        
                        <!-- Notifications widget / Academic Year -->
                        <div class="hidden lg:flex items-center gap-4">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200">
                                Année Académique: {{ date('Y') }}-{{ date('Y') + 1 }}
                            </span>
                        </div>
                    </header>
                @endisset

                <!-- Flash/Toast Alerts Component (Floating) -->
                <x-flash-messages />

                <!-- Main Layout Slot -->
                <main class="flex-1 px-4 lg:px-6 py-4 w-full mx-auto max-w-screen-2xl relative z-10">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
