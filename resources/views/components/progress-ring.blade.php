@props([
    'percent' => 0,
    'size' => 120,
    'strokeWidth' => 10,
    'colorClass' => 'text-primary'
])

@php
    $percent = max(0, min(100, (int)$percent));
    $radius = ($size - $strokeWidth) / 2;
    $circumference = 2 * pi() * $radius;
    $strokeDashoffset = $circumference - ($percent / 100) * $circumference;
@endphp

<div class="relative flex items-center justify-center" style="width: {{ $size }}px; height: {{ $size }}px;">
    <!-- SVG Ring -->
    <svg class="transform -rotate-90 shrink-0" width="{{ $size }}" height="{{ $size }}">
        <!-- Track Circle -->
        <circle 
            class="text-slate-100/80" 
            stroke="currentColor" 
            stroke-width="{{ $strokeWidth }}" 
            fill="transparent" 
            r="{{ $radius }}" 
            cx="{{ $size / 2 }}" 
            cy="{{ $size / 2 }}"
        />
        <!-- Progress Circle -->
        <circle 
            class="{{ $colorClass }} transition-all duration-1000 ease-out" 
            stroke="currentColor" 
            stroke-width="{{ $strokeWidth }}" 
            stroke-linecap="round" 
            fill="transparent" 
            r="{{ $radius }}" 
            cx="{{ $size / 2 }}" 
            cy="{{ $size / 2 }}"
            stroke-dasharray="{{ $circumference }}"
            stroke-dashoffset="{{ $strokeDashoffset }}"
        />
    </svg>
    
    <!-- Percentage Text (Overlay) -->
    <div class="absolute flex flex-col items-center justify-center text-center">
        <span class="text-2xl font-extrabold text-slate-800 tracking-tight leading-none">{{ $percent }}%</span>
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-1 block">Complété</span>
    </div>
</div>
