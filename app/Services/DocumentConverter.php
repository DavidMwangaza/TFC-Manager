<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class DocumentConverter
{
    protected string $soffice;

    public function __construct()
    {
        $this->soffice = env('SOFFICE_PATH', 'soffice');
    }

    /**
     * Convertit un document (doc/docx/odt/rtf/ppt/pptx) en PDF en utilisant LibreOffice (soffice).
     * Retourne le chemin absolu du PDF converti ou null en cas d'échec.
     */
    public function convertToPdf(string $sourcePath): ?string
    {
        $allowed = ['doc', 'docx', 'odt', 'rtf', 'ppt', 'pptx'];
        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        if (! in_array($ext, $allowed, true)) {
            return null;
        }

        $outDir = storage_path('app/public/converted');
        if (! file_exists($outDir)) {
            mkdir($outDir, 0755, true);
        }

        $filename = pathinfo($sourcePath, PATHINFO_FILENAME);
        $target = $outDir . DIRECTORY_SEPARATOR . $filename . '.pdf';

        // Si le PDF existe et est à jour, le réutiliser
        if (file_exists($target) && filemtime($target) >= filemtime($sourcePath)) {
            return $target;
        }

        // Construire et exécuter la commande soffice
        $cmd = [$this->soffice, '--headless', '--convert-to', 'pdf', '--outdir', $outDir, $sourcePath];
        $process = new Process($cmd);
        $process->setTimeout(120);

        try {
            $process->run();
        } catch (\Throwable $e) {
            return null;
        }

        if (! $process->isSuccessful()) {
            return null;
        }

        if (file_exists($target)) {
            return $target;
        }

        // Parfois LibreOffice renomme légèrement le fichier; essayer de retrouver un PDF généré
        $candidates = glob($outDir . DIRECTORY_SEPARATOR . $filename . '*.pdf');
        if (! empty($candidates)) {
            return $candidates[0];
        }

        return null;
    }
}
