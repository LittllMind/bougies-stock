<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fond extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'quantite',
        'prix_achat',
    ];

    /**
     * Nom du fond pour affichage
     */
    public function getNomAttribute(): string
    {
        $names = [
            'standard' => 'simple',
            'miroir' => 'miroir',
            'dore' => 'doré',
        ];
        return $names[$this->type] ?? $this->type;
    }
}
