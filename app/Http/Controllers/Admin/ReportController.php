<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected $pdfService;
    
    public function __construct(PDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }
    
    /**
     * Page d'accueil des rapports
     */
    public function index()
    {
        // Stats pour le dashboard rapports
        $data = $this->pdfService->generateInventoryReport();
        
        return view('admin.reports.index', [
            'stats' => $data['stats'],
            'bougies_count' => $data['bougies']->count(),
            'bougies' => $data['bougies'],
        ]);
    }
    
    /**
     * Export PDF de l'inventaire
     */
    public function inventoryPDF(Request $request)
    {
        $data = $this->pdfService->generateInventoryReport();
        $html = $this->pdfService->renderInventoryHTML($data);
        
        return $this->generatePDFResponse(
            $html,
            'inventaire_seraphie_' . now()->format('Y-m-d') . '.pdf'
        );
    }
    
    /**
     * Export PDF du rapport financier
     */
    public function financialPDF(Request $request)
    {
        $startDate = $request->query('start_date') 
            ? \Carbon\Carbon::parse($request->query('start_date')) 
            : null;
        $endDate = $request->query('end_date') 
            ? \Carbon\Carbon::parse($request->query('end_date')) 
            : null;
            
        $data = $this->pdfService->generateFinancialReport($startDate, $endDate);
        $html = $this->pdfService->renderFinancialHTML($data);
        
        return $this->generatePDFResponse(
            $html,
            'rapport_financier_' . now()->format('Y-m-d') . '.pdf'
        );
    }
    
    /**
     * Génère une réponse PDF en streaming
     * Utilise l'impression navigateur comme fallback
     */
    private function generatePDFResponse(string $html, string $filename): StreamedResponse
    {
        return response()->streamDownload(function() use ($html) {
            echo $html;
        }, $filename, [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-cache',
        ]);
    }
    
    /**
     * Rapport des alertes stock
     */
    public function alertsPDF(Request $request)
    {
        $data = $this->pdfService->generateInventoryReport();
        
        // Filtrer uniquement les alertes
        $data['bougies'] = collect($data['bougies'])->filter(fn($b) => $b->quantite <= 5)->values();
        $data['is_alerts_only'] = true;
        
        $html = $this->pdfService->renderInventoryHTML($data);
        
        return $this->generatePDFResponse(
            $html,
            'alertes_stock_' . now()->format('Y-m-d') . '.pdf'
        );
    }
}
