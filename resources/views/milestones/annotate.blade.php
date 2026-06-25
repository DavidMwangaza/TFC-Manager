<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Annotation — {{ $milestone->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <style>
        body { background: #1a1a2e; }
        #canvas-container { position: relative; display: inline-block; }
        #pdf-canvas { display: block; }
        /* Fabric JS wrapper class */
        .canvas-container { position: absolute !important; top: 0 !important; left: 0 !important; }
        .tool-btn { transition: all 0.15s; }
        .tool-btn.active { background: #4f46e5 !important; color: white !important; }
        .page-nav-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

{{-- Barre du haut --}}
<div class="bg-slate-900 border-b border-slate-700 px-4 py-3 flex items-center gap-4 flex-wrap">
    <a href="{{ route('subjects.show', $milestone->subject) }}" 
       class="text-slate-400 hover:text-white flex items-center gap-2 text-sm transition">
        ← Retour au sujet
    </a>
    <div class="h-5 border-l border-slate-600"></div>
    <h1 class="text-white font-semibold text-sm truncate max-w-xs">
        Annoter : <span class="text-blue-400">{{ $milestone->title }}</span>
    </h1>
    <span class="text-slate-500 text-xs">Étudiant : {{ $milestone->subject->student->name }}</span>

    <div class="ml-auto flex items-center gap-3 flex-wrap">
        {{-- Navigation pages --}}
        <div class="flex items-center gap-2 bg-slate-800 rounded-lg px-3 py-1.5">
            <button id="btn-prev" class="page-nav-btn text-slate-300 hover:text-white text-sm font-bold px-1" onclick="changePage(-1)">‹</button>
            <span class="text-slate-300 text-sm">Page <span id="current-page">1</span> / <span id="total-pages">?</span></span>
            <button id="btn-next" class="page-nav-btn text-slate-300 hover:text-white text-sm font-bold px-1" onclick="changePage(1)">›</button>
        </div>

        {{-- Commentaire global --}}
        <input type="text" id="global-comment" placeholder="Commentaire global (optionnel)" 
               class="bg-slate-800 border border-slate-600 text-white text-sm rounded-lg px-3 py-1.5 w-64 placeholder-gray-500 focus:outline-none focus:border-blue-500">

        {{-- Sauvegarder --}}
        <button onclick="saveAnnotation()" id="btn-save"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-1.5 rounded-lg transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            Sauvegarder l'annotation
        </button>
    </div>
</div>

<div class="flex flex-1 overflow-hidden">

    {{-- Barre d'outils gauche --}}
    <div class="bg-slate-900 border-r border-slate-700 w-16 flex flex-col items-center py-4 gap-3 shrink-0">

        {{-- Sélection --}}
        <button onclick="setTool('select')" id="tool-select" title="Sélection"
                class="tool-btn w-10 h-10 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center active">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
        </button>

        {{-- Stylo rouge --}}
        <button onclick="setTool('pen')" id="tool-pen" title="Stylo (annotations manuscrites)"
                class="tool-btn w-10 h-10 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        </button>

        {{-- Surligneur --}}
        <button onclick="setTool('highlight')" id="tool-highlight" title="Surligneur jaune"
                class="tool-btn w-10 h-10 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9.5 3A6.5 6.5 0 0116 9.5c0 1.61-.59 3.09-1.56 4.23l.27.27h.79l5 5-1.5 1.5-5-5v-.79l-.27-.27A6.516 6.516 0 019.5 16 6.5 6.5 0 013 9.5 6.5 6.5 0 019.5 3m0 2C7 5 5 7 5 9.5S7 14 9.5 14 14 12 14 9.5 12 5 9.5 5z"/></svg>
        </button>

        {{-- Zone de texte --}}
        <button onclick="setTool('text')" id="tool-text" title="Ajouter un commentaire texte"
                class="tool-btn w-10 h-10 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </button>

        {{-- Rectangle --}}
        <button onclick="setTool('rect')" id="tool-rect" title="Rectangle (encadrer)"
                class="tool-btn w-10 h-10 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/></svg>
        </button>

        <div class="w-8 border-t border-slate-700 my-1"></div>

        {{-- Couleurs --}}
        <button onclick="setColor('#ef4444')" title="Rouge" class="w-7 h-7 rounded-full bg-red-500 border-2 border-white ring-2 ring-transparent hover:ring-red-400 transition" id="color-red"></button>
        <button onclick="setColor('#3b82f6')" title="Bleu" class="w-7 h-7 rounded-full bg-blue-500 border-2 border-transparent hover:ring-2 hover:ring-blue-400 transition" id="color-blue"></button>
        <button onclick="setColor('#22c55e')" title="Vert" class="w-7 h-7 rounded-full bg-green-500 border-2 border-transparent hover:ring-2 hover:ring-green-400 transition" id="color-green"></button>
        <button onclick="setColor('#f59e0b')" title="Jaune" class="w-7 h-7 rounded-full bg-amber-400 border-2 border-transparent hover:ring-2 hover:ring-amber-300 transition" id="color-yellow"></button>

        <div class="w-8 border-t border-slate-700 my-1"></div>

        {{-- Annuler --}}
        <button onclick="undoLast()" title="Annuler la dernière action"
                class="w-10 h-10 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
        </button>

        {{-- Effacer tout --}}
        <button onclick="clearCanvas()" title="Effacer toutes les annotations de cette page"
                class="w-10 h-10 rounded-xl bg-slate-800 text-red-400 hover:bg-red-900/30 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </div>

    {{-- Zone PDF + Canvas --}}
    <div class="flex-1 overflow-auto bg-slate-800 flex items-start justify-center p-6">
        <div id="canvas-container" class="shadow-2xl rounded-lg overflow-hidden">
            <canvas id="pdf-canvas"></canvas>
            <canvas id="annotation-canvas"></canvas>
        </div>
    </div>
</div>

{{-- Toast de statut --}}
<div id="toast" class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-xl shadow-lg text-sm font-medium transition-all opacity-0 translate-y-2 pointer-events-none">
     Annotation sauvegardée !
</div>

<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const PDF_URL = '{{ asset("storage/" . $milestone->thesisFile->file_path) }}';
const SAVE_URL = '{{ route("milestones.save-annotation", $milestone) }}';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

let pdfDoc = null;
let currentPage = 1;
let totalPages = 0;
let scale = 1.5;
let fabricCanvas = null;
let currentTool = 'select';
let currentColor = '#ef4444';
// Store annotations per page
let pageAnnotations = {};

async function init() {
    pdfDoc = await pdfjsLib.getDocument(PDF_URL).promise;
    totalPages = pdfDoc.numPages;
    document.getElementById('total-pages').textContent = totalPages;
    await renderPage(currentPage);
}

async function renderPage(num) {
    const page = await pdfDoc.getPage(num);
    const viewport = page.getViewport({ scale });
    const pdfCanvas = document.getElementById('pdf-canvas');
    const ctx = pdfCanvas.getContext('2d');
    pdfCanvas.width = viewport.width;
    pdfCanvas.height = viewport.height;

    await page.render({ canvasContext: ctx, viewport }).promise;

    // Save annotations of current page before switching
    if (fabricCanvas) {
        pageAnnotations[currentPage] = JSON.stringify(fabricCanvas.toJSON());
    }

    // Init or Resize Fabric canvas
    if (!fabricCanvas) {
        const annotCanvas = document.getElementById('annotation-canvas');
        annotCanvas.width = viewport.width;
        annotCanvas.height = viewport.height;
        fabricCanvas = new fabric.Canvas('annotation-canvas', {
            isDrawingMode: false,
            width: viewport.width,
            height: viewport.height,
        });

        // Bind events ONCE
        fabricCanvas.on('mouse:down', function(opt) {
            if (currentTool === 'text' && !opt.target) {
                const p = fabricCanvas.getPointer(opt.e);
                const text = new fabric.IText('Commentaire...', {
                    left: p.x, top: p.y,
                    fontSize: 14, fill: currentColor,
                    fontFamily: 'Arial', backgroundColor: 'rgba(255,255,255,0.85)',
                    padding: 4, borderColor: currentColor,
                    editingBorderColor: currentColor,
                });
                fabricCanvas.add(text);
                fabricCanvas.setActiveObject(text);
                text.enterEditing();
                text.selectAll();
            }
            if (currentTool === 'rect' && !opt.target) {
                const p = fabricCanvas.getPointer(opt.e);
                const rect = new fabric.Rect({
                    left: p.x, top: p.y, width: 120, height: 60,
                    fill: 'transparent', stroke: currentColor, strokeWidth: 2,
                });
                fabricCanvas.add(rect);
                fabricCanvas.setActiveObject(rect);
            }
        });
    } else {
        fabricCanvas.clear();
        fabricCanvas.setWidth(viewport.width);
        fabricCanvas.setHeight(viewport.height);
    }

    fabricCanvas.freeDrawingBrush.color = currentColor;
    fabricCanvas.freeDrawingBrush.width = 3;

    // Restore annotations for this page
    if (pageAnnotations[num]) {
        fabricCanvas.loadFromJSON(pageAnnotations[num], () => fabricCanvas.renderAll());
    }

    document.getElementById('current-page').textContent = num;
    document.getElementById('btn-prev').disabled = num <= 1;
    document.getElementById('btn-next').disabled = num >= totalPages;
    applyTool();
}

function changePage(delta) {
    const newPage = currentPage + delta;
    if (newPage < 1 || newPage > totalPages) return;
    // Save current before switching
    if (fabricCanvas) pageAnnotations[currentPage] = JSON.stringify(fabricCanvas.toJSON());
    currentPage = newPage;
    renderPage(currentPage);
}

function setTool(tool) {
    currentTool = tool;
    document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove('active'));
    const btn = document.getElementById('tool-' + tool);
    if (btn) btn.classList.add('active');
    applyTool();
}

function applyTool() {
    if (!fabricCanvas) return;
    fabricCanvas.isDrawingMode = (currentTool === 'pen' || currentTool === 'highlight');
    if (currentTool === 'highlight') {
        fabricCanvas.freeDrawingBrush.color = 'rgba(253, 224, 71, 0.45)';
        fabricCanvas.freeDrawingBrush.width = 16;
    } else if (currentTool === 'pen') {
        fabricCanvas.freeDrawingBrush.color = currentColor;
        fabricCanvas.freeDrawingBrush.width = 3;
    }
    fabricCanvas.selection = (currentTool === 'select');
    fabricCanvas.forEachObject(o => { o.selectable = (currentTool === 'select'); });
}

function setColor(color) {
    currentColor = color;
    // Update active ring
    ['red','blue','green','yellow'].forEach(c => {
        document.getElementById('color-' + c).classList.remove('ring-2', 'ring-white');
    });
    if (fabricCanvas && fabricCanvas.isDrawingMode) {
        fabricCanvas.freeDrawingBrush.color = color;
    }
}

function undoLast() {
    if (!fabricCanvas) return;
    const objs = fabricCanvas.getObjects();
    if (objs.length > 0) {
        fabricCanvas.remove(objs[objs.length - 1]);
        fabricCanvas.renderAll();
    }
}

function clearCanvas() {
    if (!fabricCanvas) return;
    if (!confirm('Effacer toutes les annotations de cette page ?')) return;
    fabricCanvas.clear();
    fabricCanvas.renderAll();
}

async function saveAnnotation() {
    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    btn.textContent = 'Génération en cours...';

    // Save current page annotations
    if (fabricCanvas) pageAnnotations[currentPage] = JSON.stringify(fabricCanvas.toJSON());

    // Generate final annotated PDF page by page using PDF.js + Fabric
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ unit: 'px', format: 'a4', orientation: 'portrait' });

    for (let p = 1; p <= totalPages; p++) {
        if (p > 1) doc.addPage();

        // Render PDF page to an offscreen canvas
        const page = await pdfDoc.getPage(p);
        const viewport = page.getViewport({ scale });
        const offscreen = document.createElement('canvas');
        offscreen.width = viewport.width; offscreen.height = viewport.height;
        await page.render({ canvasContext: offscreen.getContext('2d'), viewport }).promise;

        // Draw annotations on top
        if (pageAnnotations[p]) {
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = viewport.width; tempCanvas.height = viewport.height;
            const tempFabric = new fabric.Canvas(tempCanvas, { width: viewport.width, height: viewport.height });
            await new Promise(res => tempFabric.loadFromJSON(pageAnnotations[p], () => { tempFabric.renderAll(); res(); }));
            const ctx = offscreen.getContext('2d');
            ctx.drawImage(tempFabric.getElement(), 0, 0);
            tempFabric.dispose();
        }

        const imgData = offscreen.toDataURL('image/jpeg', 0.92);
        doc.addImage(imgData, 'JPEG', 0, 0, doc.internal.pageSize.getWidth(), doc.internal.pageSize.getHeight());
    }

    const pdfBase64 = doc.output('datauristring');
    const comment = document.getElementById('global-comment').value;

    const resp = await fetch(SAVE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ pdf_data: pdfBase64, comments: comment }),
    });

    const data = await resp.json();
    if (data.success) {
        const toast = document.getElementById('toast');
        toast.classList.remove('opacity-0', 'translate-y-2');
        setTimeout(() => { window.location.href = data.redirect; }, 1500);
    } else {
        alert('Erreur lors de la sauvegarde. Veuillez réessayer.');
        btn.disabled = false;
        btn.textContent = 'Sauvegarder l\'annotation';
    }
}

init();
</script>
{{-- jsPDF pour générer le PDF final --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</body>
</html>
