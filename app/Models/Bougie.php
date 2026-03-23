<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Bougie extends Model
{
    use HasFactory;

    // ============ CONSTANTES ============

    public const FORMAT_120G = '120g';
    public const FORMAT_200G = '200g';
    public const FORMAT_300G = '300g';

    public const COLLECTION_CLASSIQUE = 'classique';
    public const COLLECTION_SAISONNIERE = 'saisonniere';
    public const COLLECTION_LUXE = 'luxe';
    public const COLLECTION_LIMITED = 'limited';

    public const TYPE_CIRE_SOJA = 'soja';
    public const TYPE_CIRE_PARAFFINE = 'paraffine';
    public const TYPE_CIRE_CIRE_ABEILLE = 'abeille';

    public const SEUIL_ALERTE_PAR_DEFAUT = 5;

    // ============ ATTRIBUTS ============

    protected $fillable = [
        'reference',
        'parfum',
        'nom',
        'collection',
        'format',
        'type_cire',
        'temps_brulure',
        'notes',
        'prix',
        'quantite',
        'seuil_alerte',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'quantite' => 'integer',
        'seuil_alerte' => 'integer',
        'temps_brulure' => 'integer',
    ];

    protected $appends = [
        'nom_complet',
        'stock_faible',
        'rupture_stock',
    ];

    // ============ RELATIONS ============

    public function mouvementsStock(): MorphMany
    {
        return $this->morphMany(MouvementStock::class, 'stockable');
    }

    public function alertesStock(): MorphMany
    {
        return $this->morphMany(StockAlert::class, 'stockable');
    }
    
    /**
     * Vérifier et créer/mettre à jour les alertes de stock
     */
    public function checkStockAlert(): void
    {
        $stock = $this->quantite;
        $seuil = $this->seuil_alerte ?? self::SEUIL_ALERTE_PAR_DEFAUT;
        
        // Si stock faible (mais pas nul) et pas d'alerte active
        if ($stock > 0 && $stock <= $seuil) {
            $this->creerAlerteStock('faible', $stock, $seuil);
        }
        // Si rupture de stock
        elseif ($stock <= 0) {
            $this->creerAlerteStock('rupture', $stock, $seuil);
        }
    }
    
    /**
     * Créer une alerte de stock si nécessaire
     */
    private function creerAlerteStock(string $type, int $stock, int $seuil): void
    {
        // Vérifier si une alerte non résolue existe déjà
        $alerteExistante = $this->alertesStock()
            ->whereNull('resolved_at')
            ->first();
            
        // Si existe mais type différent (faible → rupture), on crée nouvelle
        if ($alerteExistante && $alerteExistante->type === $type) {
            return; // Alerte déjà existante et à jour
        }
        
        // Créer nouvelle alerte
        $this->alertesStock()->create([
            'type' => $type,
            'stock_actuel' => $stock,
            'seuil' => $seuil,
            'message' => $type === 'rupture' 
                ? "RUPTURE: Stock épuisé pour {$this->nom}"
                : "Alerte: Stock faible ({$stock} restants, seuil: {$seuil})",
        ]);
    }

    // ============ ACCESSEURS ============

    public function getNomCompletAttribute(): string
    {
        return $this->nom . ' ' . $this->format;
    }

    public function getStockFaibleAttribute(): bool
    {
        // BUG POTENTIEL: Si seuil_alerte est null, ça retournera false
        // alors que ça devrait retourner true si quantite < 5 (défaut)
        $seuil = $this->seuil_alerte ?? self::SEUIL_ALERTE_PAR_DEFAUT;
        return $this->quantite > 0 && $this->quantite <= $seuil;
    }

    public function getRuptureStockAttribute(): bool
    {
        return $this->quantite <= 0;
    }

    // ============ SCOPES ============

    public function scopeStockFaible($query)
    {
        // BUG POTENTIEL: Le scope ne gère pas le seuil NULL
        return $query->whereRaw('quantite <= seuil_alerte AND quantite > 0');
    }

    public function scopeRuptureStock($query)
    {
        return $query->where('quantite', '<=', 0);
    }

    public function scopeDisponible($query)
    {
        return $query->where('quantite', '>', 0);
    }

    // ============ METHODES CALCUL STOCK ============

    /**
     * 🔴 BUG IDENTIFIÉ: Cette méthode ignore les mouvements de stock
     * et utilise uniquement la valeur stockée en base.
     * Elle devrait calculer: quantite = SUM(entrees) - SUM(sorties)
     * 
     * SOLUTION: Recalculer depuis les mouvements_stock
     */
    public function recalculerStock(): int
    {
        $entrees = $this->mouvementsStock()
            ->where('type', 'entree')
            ->sum('quantite');
        
        $sorties = $this->mouvementsStock()
            ->where('type', 'sortie')
            ->sum('quantite');
        
        return $entrees - $sorties;
    }

    public function miseAJourStockDepuisMouvements(): bool
    {
        $stockCalcule = $this->recalculerStock();
        
        if ($stockCalcule !== $this->quantite) {
            // BUG: Met à jour sans transaction ni log
            $this->update(['quantite' => $stockCalcule]);
            return true; // Stock mis à jour
        }
        
        return false; // Pas de changement
    }

    // ============ LISTS ============

    public static function formats(): array
    {
        return [
            self::FORMAT_120G => '120g (≈25h)',
            self::FORMAT_200G => '200g (≈40h)',
            self::FORMAT_300G => '300g (≈60h)',
        ];
    }

    public static function collections(): array
    {
        return [
            self::COLLECTION_CLASSIQUE => 'Collection Classique',
            self::COLLECTION_SAISONNIERE => 'Collection Saisonnière',
            self::COLLECTION_LUXE => 'Collection Luxe',
            self::COLLECTION_LIMITED => 'Édition Limitée',
        ];
    }

    public static function typesCire(): array
    {
        return [
            self::TYPE_CIRE_SOJA => 'Cire de Soja',
            self::TYPE_CIRE_PARAFFINE => 'Paraffine',
            self::TYPE_CIRE_CIRE_ABEILLE => 'Cire d\'Abeille',
        ];
    }

    /**
     * Génère la prochaine référence automatique
     */
    public static function genererReference(): string
    {
        $dernier = self::orderBy('id', 'desc')->first();
        $numero = $dernier ? ($dernier->id + 1) : 1;
        return 'BOUG-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
