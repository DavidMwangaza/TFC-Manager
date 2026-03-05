<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-breadcrumb :items="[['label' => 'Mon Profil']]" />

            {{-- Carte d'identité --}}
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 shadow sm:rounded-lg overflow-hidden">
                <div class="p-6 sm:p-8">
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
                                        @elseif($role === 'Chef Departement') bg-yellow-100 text-yellow-800
                                        @elseif($role === 'Enseignant') bg-blue-100 text-blue-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ $role }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @if($user->matricule)
                        <div class="bg-white/10 rounded-lg p-3">
                            <p class="text-indigo-200 text-xs uppercase tracking-wide">Matricule</p>
                            <p class="text-white font-semibold">{{ $user->matricule }}</p>
                        </div>
                        @endif
                        @if($user->department)
                        <div class="bg-white/10 rounded-lg p-3">
                            <p class="text-indigo-200 text-xs uppercase tracking-wide">Filière</p>
                            <p class="text-white font-semibold">{{ $user->department->name }}</p>
                        </div>
                        @endif
                        <div class="bg-white/10 rounded-lg p-3">
                            <p class="text-indigo-200 text-xs uppercase tracking-wide">Membre depuis</p>
                            <p class="text-white font-semibold">{{ $user->created_at->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
