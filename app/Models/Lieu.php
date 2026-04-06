<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lieu extends Model {
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    
    protected $table = 'lieux';
    
    protected $fillable = ['nom', 'adresse', 'ville', 'code_postal', 'latitude', 'longitude', 'actif'];
    
    public function presences() {
        return $this->hasMany(Presence::class);
    }
}
