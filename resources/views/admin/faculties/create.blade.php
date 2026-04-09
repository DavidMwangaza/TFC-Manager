<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.faculties.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Créer une faculté
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Facultés', 'url' => route('admin.faculties.index')], ['label' => 'Créer']]" />
            <div class="flex gap-6">
                <div class="hidden lg:block w-64 flex-shrink-0">
                    @include('admin.partials.sidebar')
                </div>

                <div class="flex-1">
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <form method="POST" action="{{ route('admin.faculties.store') }}" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Code --}}
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700">Code <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                           placeholder="Ex: ESIS, ECOPO..."
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 uppercase">
                                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Nom --}}
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Nom de la faculté <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           placeholder="Ex: Faculté des Sciences Informatiques"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Description --}}
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea name="description" id="description" rows="3"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <a href="{{ route('admin.faculties.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-md hover:bg-gray-300 transition">
                                    Annuler
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                                    Créer la faculté
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
