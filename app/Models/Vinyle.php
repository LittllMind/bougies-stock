<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Vinyle extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'reference',
        'nom',
        'artiste',
        'modele',
        'genre',
        'style',
        'prix',
        'quantite',
    ];

    protected $appends = [
        'image',
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->format('webp')
            ->nonQueued();

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(800)
            ->format('webp')
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
            ->useDisk('public');
        // 3 images max (validé dans le controller)
    }

    public function getImageAttribute(): string
    {
        return $this->getFirstMediaUrl('photo', 'medium') ?: '/images/no-image.png';
    }


    public function isLowStock(): bool
    {
        return $this->quantite > 0 && $this->quantite <= 3;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantite <= 0;
    }

    /**
     * Status du stock avec labels
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->quantite <= 0) {
            return 'Rupture';
        } elseif ($this->quantite <= 3) {
            return 'Faible';
        } else {
            return 'OK';
        }
    }

    /**
     * CSS class pour le statut
     */
    public function getStockStatusClassAttribute(): string
    {
        return match($this->stock_status) {
            'Rupture' => 'badge-danger',
            'Faible' => 'badge-warning',
            default => 'badge-success',
        };
    }

    public function ventes()
    {
        return $this->hasMany(LigneVente::class);
    }

    // app/Models/Vinyle.php

    /**
     * Relation : Un vinyle peut être dans plusieurs paniers
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Relation : Un vinyle peut être dans plusieurs commandes
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
