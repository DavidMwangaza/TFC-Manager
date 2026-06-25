<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.academic-years.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Créer une année académique
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Années Académiques', 'url' => route('admin.academic-years.index')], ['label' => 'Créer']]" />
            <div class="flex gap-6">
                <div class="hidden lg:block w-64 flex-shrink-0">
                    @include('admin.partials.sidebar')
                </div>

                <div class="flex-1">
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-slate-700">Nom <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           placeholder="2025-2026"
                                           class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-slate-700">Date de début <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                                           class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-slate-700">Date de fin <span class="text-red-500">*</span></label>
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                                           class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <a href="{{ route('admin.academic-years.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-300 transition">
                                    Annuler
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                                    Créer l'année académique
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
