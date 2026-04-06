<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presence extends Model {
    protected $fillable = ['lieu_id', 'date', 'heure_debut', 'heure_fin', 'actif'];
    
    public function lieu() {
        return $this->belongsTo(Lieu::class);
    }
}
