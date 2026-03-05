<x-guest-layout>
    <h2 class="text-center text-lg font-semibold text-gray-700 mb-4">Mot de passe oubli&eacute;</h2>

    <div class="mb-4 text-sm text-gray-600">
        Vous avez oubli&eacute; votre mot de passe ? Aucun probl&egrave;me. Indiquez-nous votre adresse e-mail et nous vous enverrons un lien de r&eacute;initialisation.
    </div>

    <!-- Statut de session -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Adresse Email -->
        <div>
            <x-input-label for="email" :value="__('Adresse e-mail')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="votre@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="bg-blue-700 hover:bg-blue-800">
                Envoyer le lien de r&eacute;initialisation
            </x-primary-button>
        </div>

        <div class="mt-4 text-center border-t pt-4">
            <a class="text-sm text-blue-600 hover:text-blue-800 hover:underline" href="{{ route('login') }}">
                &larr; Retour &agrave; la connexion
            </a>
        </div>
    </form>
</x-guest-layout>
