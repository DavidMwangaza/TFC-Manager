@props(['status'])

@php
    $status = strtolower(trim($status));
    
    switch ($status) {
        case 'validated':
        case 'valide':
        case 'validé':
            $classes = 'bg-green-50 text-green-700 border-green-200/60';
            $dotColor = 'bg-green-500';
            $label = 'Validé';
            $pulse = false;
            break;
            
        case 'rejected':
        case 'rejete':
        case 'rejeté':
            $classes = 'bg-red-50 text-red-700 border-red-200/60';
            $dotColor = 'bg-red-500';
            $label = 'Rejeté';
            $pulse = false;
            break;
            
        case 'submitted':
        case 'soumis':
            $classes = 'bg-blue-50 text-blue-700 border-blue-200/60';
            $dotColor = 'bg-blue-500';
            $label = 'Soumis';
            $pulse = true;
            break;
            
        case 'pending':
        case 'en attente':
        default:
            $classes = 'bg-amber-50 text-amber-700 border-amber-200/60';
            $dotColor = 'bg-amber-500';
            $label = 'En attente';
            $pulse = true;
            break;
    }
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border {$classes} transition-all duration-200"]) }}>
    <span class="relative flex h-2 w-2 shrink-0">
        @if($pulse)
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $dotColor }} opacity-75"></span>
        @endif
        <span class="relative inline-flex rounded-full h-2 w-2 {{ $dotColor }}"></span>
    </span>
    <span>{{ $label }}</span>
</span>
