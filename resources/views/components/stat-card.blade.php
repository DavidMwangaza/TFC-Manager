@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null,
    'trendColor' => 'text-green-600',
    'color' => 'border-primary',
    'delay' => '0'
])

@php
    $bgClass = 'bg-slate-50';
    $textClass = 'text-slate-600';
    $borderClass = 'border-slate-100';

    if (str_contains($color, 'primary') || str_contains($color, 'blue')) {
        $bgClass = 'bg-blue-50';
        $textClass = 'text-blue-600';
        $borderClass = 'border-blue-100';
    } elseif (str_contains($color, 'success') || str_contains($color, 'green')) {
        $bgClass = 'bg-green-50';
        $textClass = 'text-green-600';
        $borderClass = 'border-green-100';
    } elseif (str_contains($color, 'amber') || str_contains($color, 'yellow')) {
        $bgClass = 'bg-amber-50';
        $textClass = 'text-amber-600';
        $borderClass = 'border-amber-100';
    } elseif (str_contains($color, 'danger') || str_contains($color, 'red') || str_contains($color, 'animate-urgent-pulse')) {
        $bgClass = 'bg-red-50';
        $textClass = 'text-red-600';
        $borderClass = 'border-red-100';
    } elseif (str_contains($color, 'teal')) {
        $bgClass = 'bg-teal-50';
        $textClass = 'text-teal-600';
        $borderClass = 'border-teal-100';
    }
@endphp

<div class="glass-card hover-lift rounded-2xl p-6 border border-slate-150 flex flex-col justify-between group transition-all duration-300" style="animation-delay: {{ $delay }}ms;">
    <div class="flex justify-between items-start mb-6">
        @if($icon)
            <div class="w-12 h-12 {{ $bgClass }} {{ $textClass }} border {{ $borderClass }} rounded-xl flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:scale-110 shadow-inner">
                <x-icon name="{{ $icon }}" class="h-6 w-6" />
            </div>
        @endif
        @if($trend)
            <span class="text-xs font-bold {{ $trendColor }} bg-white px-2.5 py-1 rounded-lg border border-slate-100 shadow-sm flex items-center gap-1">
                {{ $trend }}
            </span>
        @endif
    </div>
    
    <div class="space-y-1.5">
        <h3 class="text-3xl font-black text-slate-800 tracking-tight leading-none group-hover:text-slate-950">{{ $value }}</h3>
        <p class="text-[11px] font-bold text-slate-450 uppercase tracking-wider">{{ $title }}</p>
    </div>
</div>
