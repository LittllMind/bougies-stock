<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Vinyle extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'nom',
        'modele',
        'prix',
        'quantite',
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
        // Ancienne collection générique (si tu veux la garder pour compat / admin)
        $this->addMediaCollection('photos')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        // 3 collections structurées
        $this->addMediaCollection('photo_standard')
            ->useDisk('public')
            ->singleFile(); // 1 image max

        $this->addMediaCollection('photo_miroir')
            ->useDisk('public')
            ->singleFile();

        $this->addMediaCollection('photo_dore')
            ->useDisk('public')
            ->singleFile();
    }


    public function isLowStock(): bool
    {
        return $this->quantite <= 5;
    }

    public function ventes()
    {
        return $this->hasMany(LigneVente::class);
    }
}
