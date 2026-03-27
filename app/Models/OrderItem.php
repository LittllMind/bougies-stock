<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'bougie_id',
        'vinyle_id',
        'fond_id',
        'titre_vinyle',
        'artiste_vinyle',
        'reference_vinyle',
        'quantite',
        'prix_unitaire',
        'total',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function bougie(): BelongsTo
    {
        return $this->belongsTo(Bougie::class);
    }
}
