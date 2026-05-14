<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aperçu — {{ $thesisFile->original_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="max-w-7xl mx-auto p-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold">Aperçu du document</h1>
                <p class="text-sm text-gray-600">{{ $thesisFile->original_name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('archives.download', $thesisFile) }}" class="px-3 py-2 bg-gray-200 rounded text-sm">Télécharger</a>
                <a href="{{ url('/') }}" class="px-3 py-2 bg-white border rounded text-sm">Retour</a>
            </div>
        </div>

        <div class="bg-white border rounded shadow-sm" style="height:80vh;">
            <iframe src="{{ route('archives.file', $thesisFile) }}" frameborder="0" style="width:100%; height:100%;"></iframe>
        </div>

        <div class="mt-4 text-sm text-gray-500">
            <p>Si l'aperçu n'est pas disponible pour ce format, utilisez le bouton « Télécharger ».</p>
        </div>
    </div>
</body>
</html>
