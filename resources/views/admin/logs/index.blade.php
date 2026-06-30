<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-slate-800 tracking-tight leading-relaxed inline-flex items-center gap-2">
            <x-icon name="clipboard-document-list" class="w-6 h-6 text-primary" /> Journal des Activités Système
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Journal des Activités']]" />

            {{-- Filtres --}}
            <div class="glass-card rounded-2xl shadow-sm bg-white/80 backdrop-blur-md border border-slate-200/60 p-5">
                <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Type d'Action</label>
                        <select name="action" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-lg focus:ring-primary focus:border-primary text-sm py-2 px-3 shadow-sm transition-all">
                            <option value="">Toutes les actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Date Début</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-lg focus:ring-primary focus:border-primary text-sm py-2 px-3 shadow-sm transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Date Fin</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-lg focus:ring-primary focus:border-primary text-sm py-2 px-3 shadow-sm transition-all">
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 inline-flex justify-center items-center gap-2 bg-primary hover:bg-primary-dark text-white text-sm font-bold py-2 px-4 rounded-lg shadow-sm hover:shadow transition-all">
                            <x-icon name="funnel" class="w-4 h-4" /> Filtrer
                        </button>
                        <a href="{{ route('admin.logs.index') }}" class="inline-flex justify-center items-center px-4 bg-white hover:bg-slate-50 border border-slate-200 text-slate-600 text-sm font-bold rounded-lg shadow-sm transition-all" title="Réinitialiser">
                            <x-icon name="arrow-path" class="w-4 h-4" />
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table des logs --}}
            <div class="glass-card rounded-2xl shadow-sm bg-white border border-slate-200/60 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-3.5 px-5">Date & Heure</th>
                                <th class="py-3.5 px-5">Utilisateur</th>
                                <th class="py-3.5 px-5">Action</th>
                                <th class="py-3.5 px-5 w-full">Détails de l'événement</th>
                                <th class="py-3.5 px-5 text-right">Adresse IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($logs as $log)
                                @php
                                    $action = strtoupper($log->action);
                                    if (in_array($action, ['AUTH_FAILED', 'DELETED', 'BLOCKED', 'REJECTED'])) {
                                        $badgeColor = 'bg-red-50 text-red-700 border-red-200';
                                        $icon = 'exclamation-circle';
                                    } elseif (in_array($action, ['LOGIN', 'TELEVERSEMENT_PDF', 'CREATED'])) {
                                        $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                                        $icon = 'information-circle';
                                    } elseif (in_array($action, ['OCTROI_FEU_VERT', 'VALIDATED', 'UNBLOCKED'])) {
                                        $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        $icon = 'check-circle';
                                    } elseif (in_array($action, ['ARCHIVAGE_VERROUILLE', 'YEAR_CLOSED'])) {
                                        $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                        $icon = 'lock-closed';
                                    } else {
                                        $badgeColor = 'bg-slate-100 text-slate-700 border-slate-200';
                                        $icon = 'chevron-right';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3 px-5 text-xs text-slate-500 font-medium">
                                        {{ $log->created_at->format('d/m/Y') }} <br/>
                                        <span class="text-slate-400">{{ $log->created_at->format('H:i:s') }}</span>
                                    </td>
                                    <td class="py-3 px-5">
                                        @if($log->user)
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                                                    {{ substr($log->user->name, 0, 1) }}
                                                </div>
                                                <span class="font-bold text-slate-800">{{ $log->user->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Système / Anonyme</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider border {{ $badgeColor }}">
                                            <x-icon name="{{ $icon }}" class="w-3.5 h-3.5" />
                                            {{ $action }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-5 text-slate-600 whitespace-normal min-w-[250px] leading-relaxed text-xs">
                                        {{ $log->description }}
                                    </td>
                                    <td class="py-3 px-5 text-right">
                                        <span class="text-xs font-mono text-slate-400 bg-slate-50 px-2 py-1.5 rounded border border-slate-150">{{ $log->ip_address ?? '—' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center border border-slate-100">
                                                <x-icon name="document-magnifying-glass" class="w-8 h-8 text-slate-300" />
                                            </div>
                                            <span class="text-slate-500 font-medium text-sm">Aucun événement ne correspond à ces critères.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($logs->hasPages())
                    <div class="p-4 border-t border-slate-200/60 bg-slate-50/50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
