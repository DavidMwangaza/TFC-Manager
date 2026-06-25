<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight inline-flex items-center gap-2">
                <x-icon name="users" class="w-6 h-6" /> Gestion des Utilisateurs
            </h2>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nouveau utilisateur
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Utilisateurs']]" />
            <div class="flex gap-6">
                {{-- Sidebar --}}
                <div class="hidden lg:block w-64 flex-shrink-0">
                    @include('admin.partials.sidebar')
                </div>

                {{-- Contenu principal --}}
                <div class="flex-1 space-y-6">
                    {{-- Messages flash --}}
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('generated_password'))
                        <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-md" x-data="{ show: true }" x-show="show">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-sm">Nouveau mot de passe :</p>
                                    <p class="font-mono text-lg mt-1 select-all">{{ session('generated_password') }}</p>
                                    <p class="text-xs mt-1 text-amber-600">Copiez et communiquez ce mot de passe maintenant. Il ne sera plus affiché.</p>
                                </div>
                                <button @click="show = false" class="text-amber-500 hover:text-amber-700 ml-4">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Filtres --}}
                    <div class="bg-white shadow-sm rounded-lg p-4">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
                                       class="w-full border-slate-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <select name="role" class="w-full border-slate-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Tous les rôles</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="department_id" class="w-full border-slate-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Toutes les filières</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>[{{ $dept->code }}] {{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="status" class="w-full border-slate-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actifs</option>
                                    <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Bloqués</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm rounded-md hover:bg-slate-700 transition">Filtrer</button>
                                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 text-sm rounded-md hover:bg-slate-300 transition">Reset</a>
                            </div>
                        </form>
                    </div>

                    {{-- Tableau --}}
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nom</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Matricule</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Rôle</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Filière</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Statut</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @forelse($users as $u)
                                        <tr class="hover:bg-slate-50 {{ $u->is_blocked ? 'bg-red-50' : '' }}">
                                            <td class="px-4 py-3 text-sm text-slate-500">{{ $u->id }}</td>
                                            <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $u->name }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $u->email }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $u->matricule ?? '—' }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                @foreach($u->roles as $role)
                                                    @php
                                                        $colors = [
                                                            'Admin' => 'bg-red-100 text-red-800',
                                                            'Chef de département' => 'bg-blue-100 text-blue-800',
                                                            'Enseignant' => 'bg-blue-100 text-blue-800',
                                                            'Etudiant' => 'bg-green-100 text-green-800',
                                                        ];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$role->name] ?? 'bg-slate-100 text-slate-800' }}">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $u->department?->name ?? '—' }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($u->is_blocked)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Bloqué</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right">
                                                <div class="flex justify-end gap-1">
                                                    <a href="{{ route('admin.users.edit', $u) }}" class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100" title="Modifier">
                                                        <x-icon name="pencil-square" class="w-3.5 h-3.5" /> Modifier
                                                    </a>

                                                    @if($u->id !== auth()->id())
                                                        {{-- Bloquer/Débloquer --}}
                                                        <form method="POST" action="{{ route('admin.users.toggle-block', $u) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-xs {{ $u->is_blocked ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }} rounded" title="{{ $u->is_blocked ? 'Débloquer' : 'Bloquer' }}">
                                                                @if($u->is_blocked)<x-icon name="lock-open" class="w-3.5 h-3.5" />@else<x-icon name="lock-closed" class="w-3.5 h-3.5" />@endif
                                                                {{ $u->is_blocked ? 'Débloquer' : 'Bloquer' }}
                                                            </button>
                                                        </form>

                                                        {{-- Réinitialiser mot de passe --}}
                                                        <form method="POST" action="{{ route('admin.users.reset-password', $u) }}" class="inline"
                                                              onsubmit="return confirm('Réinitialiser le mot de passe de {{ $u->name }} à \'password\' ?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-orange-50 text-orange-700 rounded hover:bg-orange-100" title="Réinitialiser le mot de passe">
                                                                <x-icon name="key" class="w-3.5 h-3.5" /> Réinitialiser
                                                            </button>
                                                        </form>

                                                        {{-- Supprimer --}}
                                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline"
                                                              onsubmit="return confirm('Supprimer définitivement {{ $u->name }} ?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-red-50 text-red-700 rounded hover:bg-red-100" title="Supprimer">
                                                                <x-icon name="trash" class="w-3.5 h-3.5" /> Supprimer
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">Aucun utilisateur trouvé.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="px-4 py-3 border-t">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
