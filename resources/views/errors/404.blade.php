<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page non trouvée</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-6">
        <p class="text-8xl font-extrabold text-blue-600">404</p>
        <h1 class="mt-4 text-3xl font-bold text-slate-900">Page non trouvée</h1>
        <p class="mt-2 text-slate-600">Désolé, la page que vous recherchez n'existe pas ou a été déplacée.</p>
        <div class="mt-8 flex justify-center gap-4">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Accueil
            </a>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-5 py-2.5 bg-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-300 transition">
                ← Retour
            </a>
        </div>
    </div>
</body>
</html>
