<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total',
        'mode_paiement',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function lignes()
    {
        return $this->hasMany(LigneVente::class);
    }
}
