<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight inline-flex items-center gap-2">
                <x-icon name="academic-cap" class="w-6 h-6" /> Facultés
            </h2>
            <a href="{{ route('admin.faculties.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nouvelle faculté
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Administration', 'url' => route('dashboard')], ['label' => 'Facultés']]" />
            <div class="flex gap-6">
                <div class="hidden lg:block w-64 flex-shrink-0">
                    @include('admin.partials.sidebar')
                </div>

                <div class="flex-1 space-y-6">
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">{{ session('error') }}</div>
                    @endif

                    @if($faculties->isEmpty())
                        <div class="bg-white shadow-sm rounded-lg p-8 text-center text-slate-500">
                            Aucune faculté configurée.
                            <a href="{{ route('admin.faculties.create') }}" class="text-blue-600 hover:underline">Créer la première</a>
                        </div>
                    @else
                        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Code</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nom</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Description</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Filières</th>
                                            <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @foreach($faculties as $faculty)
                                            <tr class="hover:bg-slate-50">
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-blue-100 text-blue-800">
                                                        {{ $faculty->code }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $faculty->name }}</td>
                                                <td class="px-4 py-3 text-sm text-slate-500">{{ Str::limit($faculty->description, 60) }}</td>
                                                <td class="px-4 py-3 text-sm text-slate-600">{{ $faculty->departments_count }}</td>
                                                <td class="px-4 py-3 text-sm text-right">
                                                    <div class="flex justify-end gap-1">
                                                        <a href="{{ route('admin.faculties.edit', $faculty) }}" class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 inline-flex items-center gap-1">
                                                            <x-icon name="pencil-square" class="w-3.5 h-3.5" /> Modifier
                                                        </a>
                                                        @if($faculty->departments_count === 0)
                                                            <form method="POST" action="{{ route('admin.faculties.destroy', $faculty) }}" class="inline"
                                                                  onsubmit="return confirm('Supprimer la faculté {{ $faculty->name }} ?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="px-2 py-1 text-xs bg-red-50 text-red-700 rounded hover:bg-red-100 inline-flex items-center gap-1">
                                                                    <x-icon name="trash" class="w-3.5 h-3.5" /> Supprimer
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
