<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lieu;
use Illuminate\Http\Request;

class LieuController extends Controller
{
    /**
     * Affiche la liste des lieux avec pagination.
     */
    public function index()
    {
        $lieux = Lieu::paginate(15);
        return view('admin.lieux.index', compact('lieux'));
    }

    /**
     * Affiche le formulaire de création d'un lieu.
     */
    public function create()
    {
        return view('admin.lieux.create');
    }

    /**
     * Enregistre un nouveau lieu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:500',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $validated['actif'] = $request->boolean('actif', true);

        Lieu::create($validated);

        return redirect()->route('admin.lieux.index')
            ->with('success', 'Lieu créé avec succès.');
    }
}
