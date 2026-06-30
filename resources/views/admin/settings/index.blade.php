<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight inline-flex items-center gap-2">
            <x-icon name="cog-6-tooth" class="w-6 h-6" /> Paramètres du Système
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Paramètres']]" />
            <div class="flex gap-6">

                <div class="flex-1 space-y-6">
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">{{ session('success') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Paramètres Généraux --}}
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-slate-50 px-6 py-3 border-b">
                                <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Paramètres Généraux</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                @foreach($generalSettings as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-slate-700">{{ $setting->label }}</label>
                                        <input type="text" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}"
                                               value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                               class="mt-1 block w-full max-w-lg border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        @if($setting->description)
                                            <p class="mt-1 text-xs text-slate-500">{{ $setting->description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Échéances --}}
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-amber-50 px-6 py-3 border-b">
                                <h3 class="text-sm font-semibold text-amber-800 uppercase tracking-wider">Échéances (Deadlines)</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                @foreach($deadlineSettings as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-slate-700">{{ $setting->label }}</label>
                                        <input type="date" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}"
                                               value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                               class="mt-1 block w-full max-w-xs border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        @if($setting->description)
                                            <p class="mt-1 text-xs text-slate-500">{{ $setting->description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Configuration IA --}}
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="bg-blue-50 px-6 py-3 border-b">
                                <h3 class="text-sm font-semibold text-blue-800 uppercase tracking-wider">Configuration IA (Détection Plagiat/IA)</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                @foreach($aiSettings as $setting)
                                    <div>
                                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-slate-700">{{ $setting->label }}</label>
                                        <div class="flex items-center gap-3 mt-1">
                                            <input type="number" name="settings[{{ $setting->key }}]" id="{{ $setting->key }}"
                                                   value="{{ old("settings.{$setting->key}", $setting->value) }}"
                                                   min="0" max="100"
                                                   class="block w-24 border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                            <span class="text-sm text-slate-500">%</span>
                                        </div>
                                        @if($setting->description)
                                            <p class="mt-1 text-xs text-slate-500">{{ $setting->description }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition inline-flex items-center gap-2">
                                <x-icon name="floppy-disk" class="w-5 h-5" /> Enregistrer tous les paramètres
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
