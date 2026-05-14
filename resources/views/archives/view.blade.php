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

        @php
            $ext = strtolower(pathinfo($thesisFile->file_path, PATHINFO_EXTENSION));
        @endphp

        <div class="bg-white border rounded shadow-sm overflow-auto" style="height:80vh;">
            <div id="preview" style="width:100%; height:100%;"></div>
        </div>

        <div class="mt-4 text-sm text-gray-500">
            <p id="fallback-note">Si l'aperçu n'est pas disponible pour ce format, utilisez le bouton « Télécharger ».</p>
        </div>
    </div>
    <!-- Libraries: Mammoth for DOCX, Marked for Markdown -->
    <script src="https://unpkg.com/mammoth/mammoth.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        (function(){
            const ext = '{{ $ext }}';
            const fileUrl = '{{ route('archives.file', $thesisFile) }}';
            const preview = document.getElementById('preview');
            const fallback = document.getElementById('fallback-note');
            const originalName = @json($thesisFile->original_name);

            const showMessage = (msg) => {
                preview.innerHTML = `<div class="p-6 text-sm text-gray-600">${msg}</div>`;
            };

            const imageExt = ['png','jpg','jpeg','gif','webp','svg'];

            if (ext === 'pdf') {
                preview.innerHTML = `<iframe src="${fileUrl}" frameborder="0" style="width:100%; height:100%;"></iframe>`;
                fallback.style.display = 'none';
            } else if (imageExt.indexOf(ext) !== -1) {
                preview.innerHTML = `<div class="w-full h-full flex items-center justify-center p-4"><img src="${fileUrl}" alt="${originalName}" style="max-width:100%; max-height:100%; object-fit:contain;"></div>`;
                fallback.style.display = 'none';
            } else if (ext === 'txt') {
                fetch(fileUrl).then(r=>r.text()).then(t=>{
                    preview.innerHTML = `<pre class="p-4 text-sm whitespace-pre-wrap">${escapeHtml(t)}</pre>`;
                    fallback.style.display = 'none';
                }).catch(()=> showMessage('Impossible de charger le fichier.'));
            } else if (ext === 'md' || ext === 'markdown') {
                fetch(fileUrl).then(r=>r.text()).then(t=>{
                    preview.innerHTML = `<div class="prose max-w-full p-6">${marked.parse(t)}</div>`;
                    fallback.style.display = 'none';
                }).catch(()=> showMessage('Impossible de charger le fichier.'));
            } else if (ext === 'docx') {
                // Use Mammoth in browser to convert DOCX to HTML
                fetch(fileUrl).then(r=>r.arrayBuffer()).then(ab=>{
                    mammoth.convertToHtml({arrayBuffer: ab})
                        .then(function(result){
                            preview.innerHTML = `<div class="p-6">${result.value}</div>`;
                            fallback.style.display = 'none';
                        })
                        .catch(function(err){
                            console.error(err);
                            showMessage('Aperçu indisponible (erreur de conversion).');
                        });
                }).catch(()=> showMessage('Impossible de charger le fichier.'));
            } else {
                // Unknown format: try to embed via iframe (browser may handle some types)
                preview.innerHTML = `<iframe src="${fileUrl}" frameborder="0" style="width:100%; height:100%;"></iframe>`;
            }

            function escapeHtml(str) {
                return String(str).replace(/[&<>'\"]/g, function(tag) {
                    const charsToReplace = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        "'": '&#39;',
                        '"': '&quot;'
                    };
                    return charsToReplace[tag] || tag;
                });
            }
        })();
    </script>
</body>
</html>
