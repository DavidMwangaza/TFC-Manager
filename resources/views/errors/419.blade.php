<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>419 — Session expirée</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-6">
        <p class="text-8xl font-extrabold text-amber-600">419</p>
        <h1 class="mt-4 text-3xl font-bold text-gray-900">Session expirée</h1>
        <p class="mt-2 text-gray-600">Votre session a expiré. Veuillez rafraîchir la page et réessayer.</p>
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ url()->current() }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/></svg>
                Rafraîchir la page
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                Se reconnecter
            </a>
        </div>
    </div>
</body>
</html>
