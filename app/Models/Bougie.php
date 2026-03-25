<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Modèle Bougie - Produit principal de l'application Bougies-Stock
 * 
 * Représente une bougie artisanale avec toutes ses caractéristiques
 * et son cycle de vie (stock, alertes, mouvements)
 */
class Bougie extends Model
{
    use HasFactory;

    /**
     * Seuil d'alerte par défaut
     */
    public const SEUIL_ALERTE_PAR_DEFAUT = 5;

    /**
     * Statuts de stock possibles
     */
    public const STATUS_EN_STOCK = 'en_stock';
    public const STATUS_STOCK_FAIBLE = 'stock_faible';
    public const STATUS_STOCK_EPUSE = 'epuise';
    public const STATUS_NON_DISPONIBLE = 'non_disponible';

    protected $fillable = [
        'reference',
        'image',
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
        'alertes_resolues' => 'boolean',
    ];

    /**
     * Attributes computed on demand
     */
    protected $appends = ['nom_complet', 'stock_status', 'image_url'];

    // ============================================================================
    // ACCESSEURS
    // ============================================================================

    /**
     * Nom complet pour affichage
     * Format: "Nom du Parfum - Collection"
     */
    public function getNomCompletAttribute(): string
    {
        $parts = [$this->nom];
        
        if ($this->collection) {
            $parts[] = $this->collection;
        }
        
        if ($this->format) {
            $parts[] = $this->format;
        }
        
        return implode(' - ', $parts);
    }

    /**
     * Prix formaté avec symbole €
     */
    public function getPrixFormateAttribute(): string
    {
        return number_format($this->prix, 2, ',', ' ') . ' €';
    }

    /**
     * URL de l'image pour affichage
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        return asset('storage/' . $this->image);
    }

    /**
     * Supprime l'image associée si présente
     */
    public function deleteImage(): void
    {
        if ($this->image && \Storage::disk('public')->exists($this->image)) {
            \Storage::disk('public')->delete($this->image);
        }
        $this->update(['image' => null]);
    }

    // ============================================================================
    // STOCK MANAGEMENT
    // ============================================================================

    /**
     * Vérifie si le stock est sous le seuil d'alerte
     */
    public function isStockBas(): bool
    {
        return $this->quantite <= $this->seuil_alerte && $this->quantite > 0;
    }

    /**
     * Vérifie si le stock est épuisé
     */
    public function isStockEpuise(): bool
    {
        return $this->quantite <= 0;
    }

    /**
     * Vérifie si la bougie est disponible à la vente
     */
    public function isDisponible(): bool
    {
        return $this->quantite > 0;
    }

    /**
     * Retourne le statut du stock sous forme de badge
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->quantite <= 0) {
            return self::STATUS_STOCK_EPUSE;
        }
        
        if ($this->quantite <= $this->seuil_alerte) {
            return self::STATUS_STOCK_FAIBLE;
        }
        
        return self::STATUS_EN_STOCK;
    }

    /**
     * Retourne la quantité manquante pour atteindre le seuil minimum
     */
    public function getQuantiteManquante(): int
    {
        if ($this->quantite >= $this->seuil_alerte) {
            return 0;
        }
        return $this->seuil_alerte - $this->quantite + 1;
    }

    /**
     * Décrémente le stock d'une quantité donnée
     * Retourne true si réussi, false si stock insuffisant
     */
    public function decrementerStock(int $quantite): bool
    {
        if ($this->quantite < $quantite) {
            return false;
        }
        
        $this->decrement('quantite', $quantite);
        return true;
    }

    /**
     * Incrémente le stock d'une quantité donnée
     */
    public function incrementerStock(int $quantite): void
    {
        $this->increment('quantite', $quantite);
    }

    // ============================================================================
    // ALERTES STOCK
    // ============================================================================

    /**
     * Vérifie et crée une alerte de stock si nécessaire
     * Appelé automatiquement par l'observer lors des changements
     */
    public function checkStockAlert(): void
    {
        // Ne pas vérifier si la bougie n'est pas encore en base
        if (!$this->exists) {
            return;
        }

        if (!$this->isStockBas() && !$this->isStockEpuise()) {
            return;
        }

        // Éviter les doublons - vérifier si alerte non résolue existe
        $alerteExistante = $this->stockAlerts()
            ->where('statut', 'actif')
            ->exists();

        if ($alerteExistante) {
            return;
        }

        // Créer nouvelle alerte avec colonnes conformes à la migration
        $this->stockAlerts()->create([
            'quantite_actuelle' => $this->quantite ?? 0,
            'seuil_alerte' => $this->seuil_alerte ?? 5,
            'statut' => 'actif',
        ]);
    }

    /**
     * Résoudre les alertes actives si le stock est redevenu suffisant
     */
    public function resoudreAlertesSiStockOk(): void
    {
        if ($this->isStockBas() || $this->isStockEpuise()) {
            return;
        }

        $this->stockAlerts()
            ->where('statut', 'actif')
            ->update(['statut' => 'resolu', 'resolved_at' => now()]);
    }

    // ============================================================================
    // RELATIONS
    // ============================================================================

    public function ligneVentes()
    {
        return $this->hasMany(LigneVente::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relation polymorphique pour les mouvements de stock
     */
    public function mouvementsStock()
    {
        return $this->morphMany(MouvementStock::class, 'stockable');
    }

    /**
     * Relation polymorphique pour les alertes de stock
     */
    public function stockAlerts()
    {
        return $this->morphMany(StockAlert::class, 'stockable');
    }

    // ============================================================================
    // SCOPES
    // ============================================================================

    /**
     * Scope: Bougies en stock suffisant
     */
    public function scopeEnStock($query)
    {
        return $query->where('quantite', '>', 0);
    }

    /**
     * Scope: Bougies avec stock faible
     */
    public function scopeStockFaible($query)
    {
        return $query->whereColumn('quantite', '<=', 'seuil_alerte')
                     ->where('quantite', '>', 0);
    }

    /**
     * Scope: Bougies épuisées
     */
    public function scopeEpuise($query)
    {
        return $query->where('quantite', '<=', 0);
    }

    /**
     * Scope: Bougies disponibles à la vente
     */
    public function scopeDisponibles($query)
    {
        return $query->where('quantite', '>', 0);
    }

    /**
     * Scope: Par collection
     */
    public function scopeParCollection($query, string $collection)
    {
        return $query->where('collection', $collection);
    }

    /**
     * Scope: Par format
     */
    public function scopeParFormat($query, string $format)
    {
        return $query->where('format', $format);
    }

    /**
     * Scope: Par type de cire
     */
    public function scopeParTypeCire($query, string $type)
    {
        return $query->where('type_cire', $type);
    }

    /**
     * Scope: Par parfum
     */
    public function scopeParParfum($query, string $parfum)
    {
        return $query->where('parfum', 'like', '%' . $parfum . '%');
    }

    // ============================================================================
    // METHODES UTILITAIRES
    // ============================================================================

    /**
     * Génère une référence unique si non fournie
     */
    public static function genererReference(): string
    {
        $prefix = 'BOUG';
        $date = now()->format('Ym');
        $random = strtoupper(substr(uniqid(), -4));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Liste des formats disponibles pour les bougies
     */
    public static function formatsDisponibles(): array
    {
        return ['120g', '200g', '300g', '500g'];
    }

    /**
     * Alias pour formatsDisponibles() - utilisé par les vues
     */
    public static function formats(): array
    {
        return [
            '120g' => '120g (≈25h)',
            '200g' => '200g (≈40h)',
            '300g' => '300g (≈60h)',
            '500g' => '500g (≈100h)',
        ];
    }

    /**
     * Liste des collections disponibles (positionnées 100% cire abeille)
     */
    public static function collections(): array
    {
        return [
            'Spirit' => 'Collection Spirituelle',
            'Art' => 'Collection Art',
            'Nature' => 'Collection Nature',
        ];
    }

    /**
     * Liste des types de cire disponibles
     * 
     * @deprecated Toutes les bougies sont désormais en cire d'abeille 100%
     */
    public static function typesCireDisponibles(): array
    {
        return ["cire d'abeille 100% naturelle"];
    }

    /**
     * Alias pour typesCireDisponibles()
     * 
     * @deprecated Toutes les bougies sont désormais en cire d'abeille
     */
    public static function typesCire(): array
    {
        return [
            "cire d'abeille 100% naturelle" => "Cire d'Abeille 100% Naturelle",
        ];
    }

    /**
     * Retourne les statistiques globales du stock
     */
    public static function statistiquesStock(): array
    {
        return [
            'total_bougies' => self::count(),
            'en_stock' => self::enStock()->count(),
            'stock_faible' => self::stockFaible()->count(),
            'epuise' => self::epuise()->count(),
            'alertes_actives' => StockAlert::where('resolu', false)
                ->where('stockable_type', self::class)
                ->count(),
        ];
    }

    /**
     * Boot method - événements modèle
     */
    protected static function booted(): void
    {
        static::saved(function ($bougie) {
            // Log mouvement pour debug
            Log::debug('Bougie sauvegardée', [
                'id' => $bougie->id,
                'reference' => $bougie->reference,
                'quantite' => $bougie->quantite,
            ]);
        });
    }
}
