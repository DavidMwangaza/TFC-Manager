<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-breadcrumb :items="[['label' => 'Mon Profil']]" />

            {{-- Carte de profil unifiée --}}
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                {{-- En-tête avec gradient --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 sm:p-8">
                    <div class="flex items-center space-x-6">
                        <div class="flex-shrink-0">
                            <div class="h-20 w-20 rounded-full bg-white/20 flex items-center justify-center">
                                <span class="text-3xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                        </div>
                        <div class="text-white">
                            <h3 class="text-2xl font-bold">{{ $user->name }}</h3>
                            <p class="text-indigo-100">{{ $user->email }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($user->getRoleNames() as $role)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        @if($role === 'Admin') bg-red-100 text-red-800
                                        @elseif($role === 'Chef de département') bg-yellow-100 text-yellow-800
                                        @elseif($role === 'Enseignant') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Détails du profil --}}
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-semibold text-gray-900">{{ __('Informations personnelles') }}</h4>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            {{ __('Lecture seule') }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mb-6">{{ __('Ces informations sont gérées par l\'administrateur. Contactez-le pour toute modification.') }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nom complet') }}</p>
                            <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Adresse e-mail') }}</p>
                            <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                        </div>

                        @if($user->matricule)
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Matricule') }}</p>
                            <p class="text-gray-900 font-medium">{{ $user->matricule }}</p>
                        </div>
                        @endif

                        @if($user->department)
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Filière') }}</p>
                            <p class="text-gray-900 font-medium">{{ $user->department->name }}</p>
                        </div>
                        @endif

                        <div class="space-y-1">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Rôle(s)') }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->getRoleNames() as $role)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        @if($role === 'Admin') bg-red-100 text-red-800
                                        @elseif($role === 'Chef de département') bg-yellow-100 text-yellow-800
                                        @elseif($role === 'Enseignant') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-1">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Membre depuis') }}</p>
                            <p class="text-gray-900 font-medium">{{ $user->created_at->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
