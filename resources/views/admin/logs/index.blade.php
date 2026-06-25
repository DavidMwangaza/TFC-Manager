<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-semibold text-xl text-slate-800 tracking-tight leading-relaxed inline-flex items-center gap-2">
            <x-icon name="server-stack" class="w-6 h-6 text-emerald-600" /> Console d'Audit Sécurité
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Audit System']]" />
            <div class="flex flex-col lg:flex-row gap-6 mt-4">
                <div class="hidden lg:block w-64 flex-shrink-0">
                    @include('admin.partials.sidebar')
                </div>

                <div class="flex-1 space-y-4">
                    {{-- Terminal Window --}}
                    <div class="rounded-2xl overflow-hidden shadow-[0_0_50px_-12px_rgba(16,185,129,0.3)] border border-slate-700/50 relative group bg-[#0a0f0d]">
                        
                        {{-- Fake Scanlines Effect --}}
                        <div class="absolute inset-0 pointer-events-none bg-[linear-gradient(rgba(18,16,16,0)_50%,rgba(0,0,0,0.25)_50%),linear-gradient(90deg,rgba(255,0,0,0.06),rgba(0,255,0,0.02),rgba(0,0,255,0.06))] bg-[length:100%_4px,3px_100%] z-10 opacity-20"></div>

                        {{-- Window Header (Mac Style) --}}
                        <div class="bg-[#1a1f1c] border-b border-green-900/30 p-3 flex justify-between items-center relative z-20">
                            <div class="flex gap-2.5">
                                <div class="w-3.5 h-3.5 rounded-full bg-red-500/80 border border-red-500/50 shadow-[0_0_5px_rgba(239,68,68,0.5)]"></div>
                                <div class="w-3.5 h-3.5 rounded-full bg-yellow-500/80 border border-yellow-500/50 shadow-[0_0_5px_rgba(234,179,8,0.5)]"></div>
                                <div class="w-3.5 h-3.5 rounded-full bg-green-500/80 border border-green-500/50 shadow-[0_0_5px_rgba(34,197,94,0.5)]"></div>
                            </div>
                            <div class="text-emerald-500/70 font-mono text-xs font-semibold tracking-widest flex items-center gap-2">
                                <x-icon name="shield-check" class="w-4 h-4" />
                                UDBL_SEC_AUDIT // ROOT_ACCESS
                            </div>
                            <div class="w-16"></div> {{-- Spacer for centering --}}
                        </div>

                        {{-- Terminal Body --}}
                        <div class="p-4 font-mono text-sm relative z-20">
                            
                            {{-- Filtres / Command Input --}}
                            <div class="mb-4 p-4 bg-[#0d1411] border border-emerald-900/30 rounded-lg shadow-inner">
                                <div class="text-emerald-500 mb-4 text-xs font-bold tracking-widest uppercase flex items-center gap-2 opacity-80">
                                    <span class="animate-pulse">>_</span> Query System Logs
                                </div>
                                <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                    <div class="md:col-span-3 relative">
                                        <label class="block text-[10px] text-emerald-600/70 uppercase tracking-widest mb-1">EVENT_TYPE</label>
                                        <select name="action" class="w-full bg-[#080c0a] border border-emerald-900/50 text-emerald-400 rounded focus:ring-emerald-500 focus:border-emerald-500 text-xs py-2 px-3 appearance-none shadow-inner">
                                            <option value="">[*] ALL_EVENTS</option>
                                            @foreach($actions as $action)
                                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>[{{ $action }}]</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-[10px] text-emerald-600/70 uppercase tracking-widest mb-1">TIME_START</label>
                                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full bg-[#080c0a] border border-emerald-900/50 text-emerald-400 rounded focus:ring-emerald-500 focus:border-emerald-500 text-xs py-2 px-3 shadow-inner [color-scheme:dark]">
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-[10px] text-emerald-600/70 uppercase tracking-widest mb-1">TIME_END</label>
                                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full bg-[#080c0a] border border-emerald-900/50 text-emerald-400 rounded focus:ring-emerald-500 focus:border-emerald-500 text-xs py-2 px-3 shadow-inner [color-scheme:dark]">
                                    </div>
                                    <div class="md:col-span-3 flex gap-2 h-[34px]">
                                        <button type="submit" class="flex-1 bg-emerald-900/30 hover:bg-emerald-800/50 border border-emerald-500/50 text-emerald-400 hover:text-emerald-300 text-[10px] font-bold rounded tracking-widest uppercase transition-all shadow-[0_0_10px_rgba(16,185,129,0.1)] hover:shadow-[0_0_15px_rgba(16,185,129,0.3)]">
                                            Execute
                                        </button>
                                        <a href="{{ route('admin.logs.index') }}" class="px-4 bg-[#1a1f1c] hover:bg-red-900/20 border border-slate-700 hover:border-red-500/50 text-slate-400 hover:text-red-400 text-[10px] font-bold rounded flex items-center justify-center transition-all uppercase tracking-widest">
                                            Clear
                                        </a>
                                    </div>
                                </form>
                            </div>

                            {{-- Logs Stream --}}
                            <div class="overflow-x-auto custom-scrollbar bg-[#080c0a] border border-slate-800/50 rounded-lg p-1 shadow-inner">
                                <table class="w-full text-left border-collapse whitespace-nowrap">
                                    <thead>
                                        <tr class="text-emerald-700 border-b border-emerald-900/30 text-[10px] uppercase tracking-widest">
                                            <th class="py-3 px-4 font-normal">Timestamp</th>
                                            <th class="py-3 px-4 font-normal">Source_IP</th>
                                            <th class="py-3 px-4 font-normal">Identity</th>
                                            <th class="py-3 px-4 font-normal">Sys_Event</th>
                                            <th class="py-3 px-4 font-normal w-full">Payload_Data</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-emerald-900/10">
                                        @forelse($logs as $log)
                                            @php
                                                $action = strtoupper($log->action);
                                                // Matrix of colors based on event threat/type
                                                if (in_array($action, ['AUTH_FAILED', 'DELETED', 'BLOCKED', 'REJECTED'])) {
                                                    $color = 'text-red-500 drop-shadow-[0_0_5px_rgba(239,68,68,0.6)]';
                                                    $bgHover = 'hover:bg-red-900/10';
                                                } elseif (in_array($action, ['LOGIN', 'TELEVERSEMENT_PDF', 'CREATED'])) {
                                                    $color = 'text-cyan-400 drop-shadow-[0_0_5px_rgba(34,211,238,0.5)]';
                                                    $bgHover = 'hover:bg-cyan-900/10';
                                                } elseif (in_array($action, ['OCTROI_FEU_VERT', 'VALIDATED', 'UNBLOCKED'])) {
                                                    $color = 'text-emerald-400 drop-shadow-[0_0_5px_rgba(52,211,153,0.5)]';
                                                    $bgHover = 'hover:bg-emerald-900/10';
                                                } elseif (in_array($action, ['ARCHIVAGE_VERROUILLE', 'YEAR_CLOSED'])) {
                                                    $color = 'text-amber-400 drop-shadow-[0_0_5px_rgba(251,191,36,0.5)]';
                                                    $bgHover = 'hover:bg-amber-900/10';
                                                } else {
                                                    $color = 'text-slate-300';
                                                    $bgHover = 'hover:bg-slate-800/30';
                                                }
                                            @endphp
                                            <tr class="{{ $bgHover }} transition-colors group">
                                                <td class="py-2.5 px-4 text-emerald-600/60 text-[11px] font-medium">
                                                    [{{ $log->created_at->format('y/m/d H:i:s') }}]
                                                </td>
                                                <td class="py-2.5 px-4 text-slate-500 text-[11px]">
                                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                                </td>
                                                <td class="py-2.5 px-4 text-indigo-400 text-[11px] font-semibold">
                                                    {{ $log->user?->name ? '@'.str_replace(' ', '_', strtolower($log->user->name)) : 'SYS_GUEST' }}
                                                </td>
                                                <td class="py-2.5 px-4 text-[11px] font-bold {{ $color }}">
                                                    <span class="group-hover:animate-pulse">{{ $action }}</span>
                                                </td>
                                                <td class="py-2.5 px-4 text-emerald-100/70 text-[11px] break-all whitespace-normal leading-relaxed">
                                                    <span class="text-emerald-700 mr-1">></span> {{ $log->description }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-12 text-center text-emerald-700/50 border border-dashed border-emerald-900/30 rounded bg-[#0a0f0d]">
                                                    <div class="flex flex-col items-center justify-center gap-2">
                                                        <x-icon name="magnifying-glass" class="w-8 h-8 opacity-50" />
                                                        <span class="tracking-widest uppercase text-xs">>_ EOF : NO_RECORDS_MATCHED</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Terminal Footer / Pagination --}}
                            <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="flex items-center text-emerald-500 text-xs tracking-widest">
                                    <span class="mr-2 opacity-70">root@udbl-tfc:~#</span>
                                    <span class="w-2.5 h-4 bg-emerald-500 inline-block animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                                </div>
                                <div class="terminal-pagination text-xs w-full sm:w-auto">
                                    {{ $logs->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Terminal Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #080c0a; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.2); 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.4); 
        }

        /* Pagination Surcharge */
        .terminal-pagination nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        .terminal-pagination nav > div {
            background-color: transparent !important;
            border-color: transparent !important;
        }
        .terminal-pagination p { color: #047857; font-size: 0.7rem; font-family: monospace; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0 !important; }
        .terminal-pagination a { background-color: #0d1411 !important; border-color: #064e3b !important; color: #10b981 !important; transition: all 0.2s; }
        .terminal-pagination a:hover { background-color: #064e3b !important; color: #34d399 !important; box-shadow: 0 0 10px rgba(16,185,129,0.2); }
        .terminal-pagination span[aria-current="page"] > span { background-color: #10b981 !important; border-color: #10b981 !important; color: #022c22 !important; box-shadow: 0 0 15px rgba(16,185,129,0.4); font-weight: bold; }
        .terminal-pagination svg { width: 1rem; height: 1rem; }
    </style>
</x-app-layout>
