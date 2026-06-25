@props([
    'title',
    'description',
    'icon' => 'inbox',
    'actionUrl' => null,
    'actionText' => null
])

<div class="glass-card rounded-2xl p-8 lg:p-12 text-center flex flex-col items-center justify-center max-w-xl mx-auto shadow-inner animate-fade-in-up border border-slate-100">
    <!-- Icon Wrapper -->
    <div class="p-4 bg-slate-50 text-slate-400 rounded-full border border-slate-100 shadow-sm mb-5">
        <x-icon name="{{ $icon }}" class="h-10 w-10 text-slate-400" />
    </div>
    
    <!-- Text Content -->
    <h3 class="text-lg font-bold text-slate-800 tracking-tight mb-2">{{ $title }}</h3>
    <p class="text-sm text-slate-500 leading-relaxed mb-6 max-w-sm">{{ $description }}</p>
    
    <!-- CTA Button -->
    @if($actionUrl && $actionText)
        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-light text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover-lift shadow-md shadow-primary/20 transition-all duration-200">
            <x-icon name="plus" class="h-4 w-4" />
            <span>{{ $actionText }}</span>
        </a>
    @endif
</div>
