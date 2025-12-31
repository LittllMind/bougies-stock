<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'alertable_type',
        'alertable_id',
        'quantite_actuelle',
        'seuil_alerte',
        'statut',
        'derniere_notification_envoyee',
    ];

    protected $casts = [
        'derniere_notification_envoyee' => 'datetime',
    ];

    /**
     * Relation polymorphique vers Vinyle ou Fond
     */
    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope : alertes non résolues
     */
    public function scopeActives($query)
    {
        return $query->where('statut', 'actif');
    }

    /**
     * Marquer comme résolu (quand stock réapprovisionné)
     */
    public function marquerResolu(): void
    {
        $this->update(['statut' => 'resolu']);
    }
}
