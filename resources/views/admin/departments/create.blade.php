<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.departments.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Créer une filière
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Filières', 'url' => route('admin.departments.index')], ['label' => 'Créer']]" />
            <div class="flex gap-6">
                <div class="hidden lg:block w-64 flex-shrink-0">
                    @include('admin.partials.sidebar')
                </div>

                <div class="flex-1">
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Faculté --}}
                                <div>
                                    <label for="faculty_id" class="block text-sm font-medium text-slate-700">Faculté <span class="text-red-500">*</span></label>
                                    <select name="faculty_id" id="faculty_id" required
                                            class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">— Choisir une faculté —</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                                [{{ $faculty->code }}] {{ $faculty->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('faculty_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Code --}}
                                <div>
                                    <label for="code" class="block text-sm font-medium text-slate-700">Code <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="code" value="{{ old('code') }}" required
                                           placeholder="Ex: GL, RAS, DM..."
                                           class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 uppercase">
                                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Nom --}}
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-slate-700">Nom de la filière <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           placeholder="Ex: Génie Logiciel"
                                           class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Description --}}
                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                                    <textarea name="description" id="description" rows="3"
                                              class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <a href="{{ route('admin.departments.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-300 transition">
                                    Annuler
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                                    Créer la filière
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
