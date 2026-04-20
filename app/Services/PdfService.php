<?php

namespace App\Services;

/**
 * Service de génération PDF avec fallback HTML
 * 
 * Utilise DomPDF quand disponible, sinon génère du HTML téléchargeable
 */
class PdfService
{
    /**
     * Génère un PDF ou retourne HTML si PDF non disponible
     */
    public static function generate(string $view, array $data, string $filename): mixed
    {
        // Vérifier si DomPDF est disponible
        if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data);
            return $pdf->download($filename);
        }

        // Fallback: retourne HTML avec en-tête PDF
        $html = view($view, $data)->render();
        
        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.html"',
        ]);
    }

    /**
     * Vérifie si la génération PDF est disponible
     */
    public static function isAvailable(): bool
    {
        return class_exists('Barryvdh\DomPDF\Facade\Pdf');
    }

    /**
     * Retourne le type de contenu disponible
     */
    public static function getContentType(): string
    {
        return self::isAvailable() ? 'application/pdf' : 'text/html';
    }
}
