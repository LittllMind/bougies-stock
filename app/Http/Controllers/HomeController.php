<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\Request;

class HomeController extends Controller {
    public function index() {
        // Récupérer présence du jour
        $presence = Presence::with('lieu')
            ->whereDate('date', today())
            ->where('actif', true)
            ->first();
        
        return view('home', compact('presence'));
    }
}
