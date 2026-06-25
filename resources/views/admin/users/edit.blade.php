<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Modifier — {{ $user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Utilisateurs', 'url' => route('admin.users.index')], ['label' => 'Modifier']]" />
            <div class="flex gap-6">
                <div class="hidden lg:block w-64 flex-shrink-0">
                    @include('admin.partials.sidebar')
                </div>

                <div class="flex-1">
                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Nom --}}
                                <div>
                                    <label for="name" class="block text-sm font-medium text-slate-700">Nom complet <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                           class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-700">Adresse email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                           class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Matricule --}}
                                <div>
                                    <label for="matricule" class="block text-sm font-medium text-slate-700">Matricule</label>
                                    <input type="text" name="matricule" id="matricule" value="{{ old('matricule', $user->matricule) }}"
                                           class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('matricule') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Rôle --}}
                                <div>
                                    <label for="role" class="block text-sm font-medium text-slate-700">Rôle <span class="text-red-500">*</span></label>
                                    <select name="role" id="role" required
                                            class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                {{-- Département --}}
                                <div class="md:col-span-2">
                                    <label for="department_id" class="block text-sm font-medium text-slate-700">Filière</label>
                                    <select name="department_id" id="department_id"
                                            class="mt-1 block w-full border-slate-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">— Aucun —</option>
                                        @foreach($departments->groupBy(fn($d) => $d->faculty?->name ?? 'Sans faculté') as $faculty => $depts)
                                            <optgroup label="{{ $faculty }}">
                                                @foreach($depts as $dept)
                                                    <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                                        [{{ $dept->code }}] {{ $dept->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('department_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Infos complémentaires --}}
                            <div class="bg-slate-50 rounded-md p-4 text-sm text-slate-600">
                                <p><strong>Inscrit le :</strong> {{ $user->created_at->format('d/m/Y à H:i') }}</p>
                                <p><strong>Dernière modification :</strong> {{ $user->updated_at->format('d/m/Y à H:i') }}</p>
                                <p><strong>Statut :</strong>
                                    @if($user->is_blocked)
                                        <span class="text-red-600 font-medium">Bloqué</span>
                                    @else
                                        <span class="text-green-600 font-medium">Actif</span>
                                    @endif
                                </p>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-300 transition">
                                    Annuler
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                                    Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
