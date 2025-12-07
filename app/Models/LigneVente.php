<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneVente extends Model
{
    use HasFactory;

    protected $fillable = [
        'vente_id',
        'vinyle_id',
        'quantite',
        'prix_unitaire',
        'total',
        'fond',
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function vinyle()
    {
        return $this->belongsTo(Vinyle::class);
    }
}
