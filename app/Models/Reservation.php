<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model {
    protected $fillable = ['client_nom', 'client_telephone', 'client_email', 'date', 'heure', 'prestation', 'prix', 'statut', 'notes'];
}
