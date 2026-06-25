<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Subject;
use App\Models\ThesisFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Services\DocumentConverter;

class ArchiveController extends Controller
{
    /**
     * Afficher les travaux défendus (accès public).
     */
    public function index(Request $request)
    {
        $query = Subject::where('defense_validated', true)
            ->with(['student', 'teacher', 'department', 'academicYear', 'thesisFiles']);

        if ($request->filled('search')) {
            $search = $request->search;

            if ($request->boolean('semantic')) {
                $semanticService = new \App\Services\SemanticSearchService();
                // On a besoin de récupérer tous les sujets filtrés par les autres critères pour calculer le TF-IDF global
                // Pour optimiser, on va appliquer les autres filtres avant.
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhereHas('student', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            }
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Si la recherche sémantique est activée, on doit l'appliquer en dernier sur le corpus filtré
        if ($request->filled('search') && $request->boolean('semantic')) {
            $semanticService = new \App\Services\SemanticSearchService();
            $allFilteredSubjects = (clone $query)->get();
            $searchResults = $semanticService->search($request->search, $allFilteredSubjects);
            
            $matchedIds = collect($searchResults)->pluck('subject_id')->toArray();
            
            if (empty($matchedIds)) {
                $query->where('id', -1);
            } else {
                $query->whereIn('id', $matchedIds);
                $idsOrdered = implode(',', $matchedIds);
                $query->orderByRaw("FIELD(id, $idsOrdered)");
            }
            
            // Passer les scores sémantiques à la vue pour affichage
            $semanticScores = collect($searchResults)->pluck('score', 'subject_id')->toArray();
            view()->share('semanticScores', $semanticScores);
        }

        if (!$request->boolean('semantic') || empty($request->search)) {
            $query->latest();
        }

        $subjects = $query->paginate(12)->withQueryString();
        $departments = Department::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('archives.index', compact('subjects', 'departments', 'academicYears'));
    }

    /**
     * Télécharger un fichier d'un travail défendu (accès public).
     */
    public function download(ThesisFile $thesisFile)
    {
        // Seuls les fichiers finaux de sujets défendus sont téléchargeables publiquement
        if (!$thesisFile->subject || !$thesisFile->subject->defense_validated || $thesisFile->version_type !== 'final') {
            abort(403, 'Ce fichier n\'est pas disponible au téléchargement.');
        }

        return Storage::disk('public')->download(
            $thesisFile->file_path,
            $thesisFile->original_name
        );
    }

    /**
     * Afficher une page d'aperçu (embed) pour un fichier d'archive.
     */
    public function view(ThesisFile $thesisFile)
    {
        if (!$thesisFile->subject || !$thesisFile->subject->defense_validated || $thesisFile->version_type !== 'final') {
            abort(403, 'Ce fichier n\'est pas disponible pour visualisation.');
        }

        $path = Storage::disk('public')->path($thesisFile->file_path);
        if (!file_exists($path)) {
            abort(404);
        }

        $mime = @mime_content_type($path) ?: Storage::disk('public')->mimeType($thesisFile->file_path) ?? 'application/octet-stream';

        // Pour les PDF et images, on affiche directement une page contenant un iframe vers la ressource
        return view('archives.view', compact('thesisFile', 'mime'));
    }

    /**
     * Retourne le fichier en inline (Content-Disposition: inline) pour embedding.
     */
    public function file(ThesisFile $thesisFile)
    {
        if (!$thesisFile->subject || !$thesisFile->subject->defense_validated || $thesisFile->version_type !== 'final') {
            abort(403, 'Ce fichier n\'est pas disponible pour visualisation.');
        }

        $path = Storage::disk('public')->path($thesisFile->file_path);
        if (!file_exists($path)) {
            abort(404);
        }

        // Si le fichier est un document bureautique, tenter une conversion serveur via LibreOffice
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $convertible = ['doc', 'docx', 'odt', 'rtf', 'ppt', 'pptx'];

        if (in_array($ext, $convertible, true)) {
            $converter = app(DocumentConverter::class);
            $converted = $converter->convertToPdf($path);
            if ($converted && file_exists($converted)) {
                return response()->file($converted, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . pathinfo($thesisFile->original_name, PATHINFO_FILENAME) . '.pdf"',
                ]);
            }
        }

        $mime = @mime_content_type($path) ?: Storage::disk('public')->mimeType($thesisFile->file_path) ?? 'application/octet-stream';

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $thesisFile->original_name . '"',
        ]);
    }
    /**
     * Export OAI-PMH (Open Archives Initiative — Protocol for Metadata Harvesting).
     *
     * Génère un flux XML conforme OAI-PMH v2.0 (verbe ListRecords, format oai_dc)
     * listant les métadonnées de tous les travaux TFC/Mémoires défendus.
     * Endpoint public : GET /archives/oai
     *
     * @see https://www.openarchives.org/OAI/openarchivesprotocol.html
     */
    public function oaiPmh(Request $request): Response
    {
        $subjects = Subject::where('defense_validated', true)
            ->with(['student', 'teacher', 'department.faculty', 'academicYear'])
            ->latest('defense_date')
            ->get();

        $baseUrl   = url('/archives/oai');
        $repoName  = config('app.name', 'UDBL TFC Manager');
        $adminEmail = config('mail.from.address', 'admin@udbl-tfc.cd');
        $now       = now()->toIso8601String();

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        // Racine <OAI-PMH>
        $root = $xml->createElementNS('http://www.openarchives.org/OAI/2.0/', 'OAI-PMH');
        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $root->setAttributeNS(
            'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd'
        );
        $xml->appendChild($root);

        // <responseDate>
        $root->appendChild($xml->createElement('responseDate', $now));

        // <request verb="ListRecords">
        $reqEl = $xml->createElement('request', $baseUrl);
        $reqEl->setAttribute('verb', 'ListRecords');
        $reqEl->setAttribute('metadataPrefix', 'oai_dc');
        $root->appendChild($reqEl);

        // <ListRecords>
        $listRecords = $xml->createElement('ListRecords');
        $root->appendChild($listRecords);

        foreach ($subjects as $subject) {
            // Datestamp : date de soutenance ou date de création si non définie
            $datestamp = $subject->defense_date
                ? $subject->defense_date->toDateString()
                : $subject->created_at->toDateString();

            // Identifiant OAI unique
            $oaiId = 'oai:udbl-tfc.cd:subject/' . $subject->id;

            // <record>
            $record = $xml->createElement('record');
            $listRecords->appendChild($record);

            // <header>
            $header = $xml->createElement('header');
            $header->appendChild($xml->createElement('identifier', $oaiId));
            $header->appendChild($xml->createElement('datestamp', $datestamp));
            $header->appendChild($xml->createElement('setSpec', 'udbl_tfc'));
            $record->appendChild($header);

            // <metadata>
            $metadata = $xml->createElement('metadata');
            $record->appendChild($metadata);

            // <oai_dc:dc> avec namespaces Dublin Core
            $dc = $xml->createElementNS(
                'http://www.openarchives.org/OAI/2.0/oai_dc/',
                'oai_dc:dc'
            );
            $dc->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:dc',
                'http://purl.org/dc/elements/1.1/'
            );
            $dc->setAttributeNS(
                'http://www.w3.org/2000/xmlns/',
                'xmlns:xsi',
                'http://www.w3.org/2001/XMLSchema-instance'
            );
            $dc->setAttributeNS(
                'http://www.w3.org/2001/XMLSchema-instance',
                'xsi:schemaLocation',
                'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd'
            );
            $metadata->appendChild($dc);

            // dc:title
            $dc->appendChild($xml->createElementNS(
                'http://purl.org/dc/elements/1.1/', 'dc:title', htmlspecialchars($subject->title)
            ));

            // dc:creator (étudiant auteur)
            if ($subject->student) {
                $dc->appendChild($xml->createElementNS(
                    'http://purl.org/dc/elements/1.1/', 'dc:creator',
                    htmlspecialchars($subject->student->name)
                ));
            }

            // dc:contributor (directeur de mémoire)
            if ($subject->teacher) {
                $dc->appendChild($xml->createElementNS(
                    'http://purl.org/dc/elements/1.1/', 'dc:contributor',
                    htmlspecialchars($subject->teacher->name)
                ));
            }

            // dc:description (résumé / description)
            if ($subject->description) {
                $dc->appendChild($xml->createElementNS(
                    'http://purl.org/dc/elements/1.1/', 'dc:description',
                    htmlspecialchars($subject->description)
                ));
            }

            // dc:subject (filière + faculté)
            if ($subject->department) {
                $dc->appendChild($xml->createElementNS(
                    'http://purl.org/dc/elements/1.1/', 'dc:subject',
                    htmlspecialchars($subject->department->name)
                ));
                if ($subject->department->faculty) {
                    $dc->appendChild($xml->createElementNS(
                        'http://purl.org/dc/elements/1.1/', 'dc:subject',
                        htmlspecialchars($subject->department->faculty->name)
                    ));
                }
            }

            // dc:date (date de soutenance)
            $dc->appendChild($xml->createElementNS(
                'http://purl.org/dc/elements/1.1/', 'dc:date', $datestamp
            ));

            // dc:type (TFC ou Mémoire)
            $type = $subject->subject_type === 'memoire' ? 'Mémoire' : 'Travail de Fin de Cycle';
            $dc->appendChild($xml->createElementNS(
                'http://purl.org/dc/elements/1.1/', 'dc:type', $type
            ));

            // dc:format
            $dc->appendChild($xml->createElementNS(
                'http://purl.org/dc/elements/1.1/', 'dc:format', 'application/pdf'
            ));

            // dc:language
            $dc->appendChild($xml->createElementNS(
                'http://purl.org/dc/elements/1.1/', 'dc:language', 'fr'
            ));

            // dc:publisher
            $dc->appendChild($xml->createElementNS(
                'http://purl.org/dc/elements/1.1/', 'dc:publisher',
                'Université Don Bosco de Lubumbashi (UDBL)'
            ));

            // dc:relation (année académique)
            if ($subject->academicYear) {
                $dc->appendChild($xml->createElementNS(
                    'http://purl.org/dc/elements/1.1/', 'dc:relation',
                    htmlspecialchars('Année académique : ' . $subject->academicYear->name)
                ));
            }

            // dc:identifier (URL de la page archive)
            $dc->appendChild($xml->createElementNS(
                'http://purl.org/dc/elements/1.1/', 'dc:identifier',
                url('/archives')
            ));

            // dc:rights
            $dc->appendChild($xml->createElementNS(
                'http://purl.org/dc/elements/1.1/', 'dc:rights',
                'Tous droits réservés — UDBL ' . now()->year
            ));
        }

        return response($xml->saveXML(), 200, [
            'Content-Type'        => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="oai_udbl_tfc.xml"',
        ]);
    }
}
