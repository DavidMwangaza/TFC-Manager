<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aperçu — {{ $thesisFile->original_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 text-zinc-800">
    {{-- BARRE INSTITUTIONNELLE --}}
    <div class="bg-gradient-to-r from-slate-900 via-primary-dark to-slate-900 text-slate-300 text-[10px] sm:text-xs py-2 shadow-sm relative z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span class="font-medium tracking-wide">Université Don Bosco de Lubumbashi &mdash; Année Académique {{ date('Y') }}-{{ date('Y') + 1 }}</span>
            <span class="flex items-center gap-1.5 uppercase tracking-wider font-bold text-accent text-[9px]">
                <span class="w-1.5 h-1.5 bg-accent rounded-full animate-ping"></span>
                <span>Portail académique sécurisé</span>
            </span>
        </div>
    </div>

    {{-- NAVBAR --}}
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-40 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-3 hover:opacity-80 transition">
                    <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-12 w-12 object-contain bg-slate-50 p-1 rounded-xl shadow-inner border border-slate-100">
                    <div>
                        <span class="text-lg font-extrabold text-slate-900 tracking-tight block leading-tight">TFC Manager</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider leading-none">Archives des Travaux</span>
                    </div>
                </a>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('archives.index') }}" class="text-slate-650 hover:text-slate-950 font-bold py-2.5 px-4 text-xs transition-colors">
                        &larr; Retour aux archives
                    </a>
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="bg-primary hover:bg-primary-light text-white text-xs font-bold py-2.5 px-5 rounded-xl hover-lift shadow-md shadow-primary/10 transition-all">
                            Mon espace
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="bg-primary hover:bg-primary-light text-white text-xs font-bold py-2.5 px-5 rounded-xl hover-lift shadow-md shadow-primary/10 transition-all">
                            Connexion
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-4">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold">Aperçu du document</h1>
                <p class="text-sm text-slate-600">{{ $thesisFile->original_name }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('archives.download', $thesisFile) }}" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg text-sm hover:bg-blue-700 transition">Télécharger</a>
                <a href="{{ route('archives.index') }}" class="px-4 py-2 bg-white border border-slate-300 font-semibold rounded-lg text-sm hover:bg-slate-50 transition">Retour</a>
            </div>
        </div>

        @php
            $ext = strtolower(pathinfo($thesisFile->file_path, PATHINFO_EXTENSION));
        @endphp

        <div class="bg-white border rounded shadow-sm overflow-auto" style="height:80vh;">
            <div id="preview" style="width:100%; height:100%;"></div>
        </div>

        <div class="mt-4 text-sm text-slate-500">
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
                preview.innerHTML = `<div class="p-6 text-sm text-slate-600">${msg}</div>`;
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
