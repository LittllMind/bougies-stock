<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_commande',
        'user_id',
        'total',
        'statut',
        'status',
        'nom',
        'prenom',
        'email',
        'telephone',
        'adresse',
        'code_postal',
        'ville',
        'shipping_nom',
        'shipping_prenom',
        'shipping_email',
        'shipping_telephone',
        'shipping_adresse',
        'shipping_code_postal',
        'shipping_ville',
        'shipping_pays',
        'shipping_instructions',
        'billing_nom',
        'billing_prenom',
        'billing_email',
        'billing_telephone',
        'billing_adresse',
        'billing_code_postal',
        'billing_ville',
        'billing_pays',
        'shipping_address',
        'stripe_session_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessor pour compatibilité status (lecture) - évite boucle avec getAttribute
    public function getStatusAttribute(): ?string
    {
        return $this->attributes['statut'] ?? null;
    }

    // Mutateur pour compatibilité status (écriture) - écrit dans statut
    public function setStatusAttribute($value): void
    {
        $this->attributes['statut'] = $value;
    }

    /**
     * Vérifier si la commande est payée
     */
    public function isPaid(): bool
    {
        return $this->statut === 'payee' || $this->statut === 'paid';
    }

    /**
     * Marquer la commande comme payée
     */
    public function markAsPaid(): void
    {
        $this->update([
            'statut' => 'payee',
            'validee_at' => now()
        ]);
    }
}
