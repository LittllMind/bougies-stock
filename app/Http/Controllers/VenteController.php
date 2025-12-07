<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\LigneVente;
use App\Models\Vinyle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('storeFromKiosque');
    }

    public function index(Request $request)
    {
        // Date demandée en query string ?date=YYYY-MM-DD
        $dateParam = $request->query('date');

        if ($dateParam) {
            try {
                $currentDateString = Carbon::parse($dateParam)->toDateString();
            } catch (\Exception $e) {
                $currentDateString = Vente::max('date') ?? now()->toDateString();
            }
        } else {
            // Par défaut : dernier jour où il y a eu une vente, sinon aujourd’hui
            $currentDateString = Vente::max('date') ?? now()->toDateString();
        }

        $currentDate = Carbon::parse($currentDateString);

        // Ventes du jour
        $ventes = Vente::with('lignes.vinyle')
            ->whereDate('date', $currentDateString)
            ->orderBy('created_at')
            ->get();

        // Stats globales
        $caTotal = $ventes->sum('total');

        $caParMode = $ventes
            ->groupBy('mode_paiement')
            ->map(function ($ventesMode) {
                return $ventesMode->sum('total');
            });

        $lignes = $ventes->flatMap->lignes;

        $nbVinylesTotal = $lignes->sum('quantite');
        $nbMiroirs = $lignes->where('fond', 'miroir')->sum('quantite');

        // Stats par artiste (nom + modèle)
        $parArtiste = $lignes
            ->groupBy(function ($ligne) {
                $vinyle = $ligne->vinyle;
                if (!$vinyle) {
                    return 'Inconnu';
                }
                return $vinyle->nom . ($vinyle->modele ? ' - ' . $vinyle->modele : '');
            })
            ->map(function ($lignesArtiste) {
                return [
                    'quantite' => $lignesArtiste->sum('quantite'),
                    'ca'       => $lignesArtiste->sum('total'),
                ];
            })
            ->sortByDesc('ca');

        // Stats par type de fond
        $parFond = $lignes
            ->groupBy(function ($ligne) {
                return $ligne->fond ?? 'standard';
            })
            ->map(function ($lignesFond) {
                return [
                    'quantite' => $lignesFond->sum('quantite'),
                    'ca'       => $lignesFond->sum('total'),
                ];
            });

        // Navigation jours précédent / suivant (où il y a des ventes)
        $previousDate = Vente::whereDate('date', '<', $currentDateString)->max('date');
        $nextDate     = Vente::whereDate('date', '>', $currentDateString)->min('date');

        return view('ventes.index', compact(
            'ventes',
            'currentDate',
            'caTotal',
            'caParMode',
            'nbVinylesTotal',
            'nbMiroirs',
            'parArtiste',
            'parFond',
            'previousDate',
            'nextDate'
        ));
    }

    public function create()
    {
        $vinyles = Vinyle::where('quantite', '>', 0)
            ->orderBy('nom')
            ->get();

        return view('ventes.create', compact('vinyles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'mode_paiement' => 'required|string|in:especes,carte,cheque',
            'vinyles' => 'required|array|min:1',
            'vinyles.*.id' => 'required|exists:vinyles,id',
            'vinyles.*.quantite' => 'required|integer|min:1',
            'vinyles.*.fond' => 'required|string|in:standard,miroir,dore',
        ]);

        // mêmes suppléments que dans ton JS
        $fondSupplements = [
            'standard' => 0,
            'miroir'   => 8,
            'dore'     => 13,
        ];

        DB::beginTransaction();

        try {
            $total = 0;
            $lignes = [];

            // 1. Calculer le total, vérifier les stocks et préparer les lignes
            foreach ($validated['vinyles'] as $item) {
                $vinyle   = Vinyle::findOrFail($item['id']);
                $quantite = (int) $item['quantite'];
                $fond     = $item['fond'] ?? 'standard';

                if ($vinyle->quantite < $quantite) {
                    throw new \Exception("Stock insuffisant pour {$vinyle->nom}");
                }

                $supplement   = $fondSupplements[$fond] ?? 0;
                $prixUnitaire = $vinyle->prix + $supplement;
                $totalLigne   = $prixUnitaire * $quantite;

                $total += $totalLigne;

                // on garde tout en mémoire pour la deuxième boucle
                $lignes[] = [
                    'vinyle'       => $vinyle,
                    'quantite'     => $quantite,
                    'fond'         => $fond,
                    'prix_unitaire' => $prixUnitaire,
                    'total'        => $totalLigne,
                ];
            }

            // 2. Créer la vente
            $vente = Vente::create([
                'date'          => $validated['date'],
                'total'         => $total,
                'mode_paiement' => $validated['mode_paiement'],
            ]);

            // 3. Créer les lignes de vente + décrémenter les stocks
            foreach ($lignes as $ligne) {
                LigneVente::create([
                    'vente_id'      => $vente->id,
                    'vinyle_id'     => $ligne['vinyle']->id,
                    'quantite'      => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'total'         => $ligne['total'],
                    'fond'          => $ligne['fond'],
                ]);

                $ligne['vinyle']->decrement('quantite', $ligne['quantite']);
            }

            DB::commit();

            return redirect()
                ->route('ventes.index')
                ->with('success', 'Vente enregistrée avec succès');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }


    public function show(Vente $vente)
    {
        $vente->load('lignes.vinyle');
        return view('ventes.show', compact('vente'));
    }

    public function destroy(Vente $vente)
    {
        // Restaurer les stocks
        foreach ($vente->lignes as $ligne) {
            $ligne->vinyle->increment('quantite', $ligne->quantite);
        }

        $vente->delete();

        return redirect()->route('ventes.index')
            ->with('success', 'Vente annulée et stocks restaurés');
    }

    public function storeFromKiosque(Request $request)
    {
        $validated = $request->validate([
            'mode_paiement'      => 'required|string|in:especes,carte,cheque',
            'vinyles'            => 'required|array|min:1',
            'vinyles.*.id'       => 'required|exists:vinyles,id',
            'vinyles.*.quantite' => 'required|integer|min:1',
            'vinyles.*.fond'     => 'required|string|in:standard,miroir,dore',
        ]);

        // Même logique que dans ton seeder
        $fondSupplements = [
            'standard' => 0,
            'miroir'   => 8,
            'dore'     => 13,
        ];

        DB::beginTransaction();

        try {
            $totalVente = 0;

            // 1) Calculer le total et vérifier les stocks
            foreach ($validated['vinyles'] as $item) {
                $vinyle = Vinyle::find($item['id']);

                if ($vinyle->quantite < $item['quantite']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuffisant pour {$vinyle->nom}",
                    ], 400);
                }

                $fond = $item['fond'] ?? 'standard';
                $supplement = $fondSupplements[$fond] ?? 0;
                $prixUnitaire = $vinyle->prix + $supplement;

                $totalVente += $prixUnitaire * $item['quantite'];
            }

            // 2) Créer la vente
            $vente = Vente::create([
                'date'          => now()->toDateString(),
                'total'         => $totalVente,
                'mode_paiement' => $validated['mode_paiement'],
            ]);

            // 3) Créer les lignes + décrémenter les stocks
            foreach ($validated['vinyles'] as $item) {
                $vinyle = Vinyle::find($item['id']);

                $fond = $item['fond'] ?? 'standard';
                $supplement = $fondSupplements[$fond] ?? 0;
                $prixUnitaire = $vinyle->prix + $supplement;
                $totalLigne = $prixUnitaire * $item['quantite'];

                LigneVente::create([
                    'vente_id'      => $vente->id,
                    'vinyle_id'     => $vinyle->id,
                    'quantite'      => $item['quantite'],
                    'prix_unitaire' => $prixUnitaire,
                    'total'         => $totalLigne,
                    'fond'          => $fond,
                ]);

                // Décrémenter le stock
                $vinyle->decrement('quantite', $item['quantite']);
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Vente enregistrée avec succès',
                'vente_id' => $vente->id,
                'total'    => $vente->total,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
