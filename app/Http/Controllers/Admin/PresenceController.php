<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\Lieu;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function index()
    {
        $presences = Presence::with('lieu')->orderBy('date', 'desc')->take(30)->get();
        $lieux = Lieu::where('actif', true)->get();
        return view('admin.presences', compact('presences', 'lieux'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lieu_id' => 'required|exists:lieux,id',
            'date' => 'required|date',
            'heure_debut' => 'required',
            'heure_fin' => 'required',
        ]);

        Presence::create($validated);
        return redirect()->route('admin.presences')->with('success', 'Présence ajoutée');
    }
}
