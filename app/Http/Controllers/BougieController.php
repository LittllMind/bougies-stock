<?php

namespace App\Http\Controllers;

use App\Models\Bougie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BougieController extends Controller
{
    /**
     * Affiche la liste des bougies (admin)
     */
    public function index()
    {
        $bougies = Bougie::paginate(20);
        return view('admin.bougies.index', compact('bougies'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.bougies.create', [
            'formats' => Bougie::formats(),
            'collections' => Bougie::collections(),
            'typesCire' => Bougie::typesCire(),
        ]);
    }

    /**
     * Enregistre une nouvelle bougie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:50|unique:bougies,reference',
            'parfum' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'collection' => 'nullable|string|max:255',
            'format' => 'nullable|string|max:50',
            'type_cire' => 'nullable|string|max:50',
            'temps_brulure' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'quantite' => 'nullable|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
        ]);

        // Définir des valeurs par défaut si non fournies
        $validated['quantite'] = $validated['quantite'] ?? 0;
        $validated['seuil_alerte'] = $validated['seuil_alerte'] ?? Bougie::SEUIL_ALERTE_PAR_DEFAUT;

        $bougie = Bougie::create($validated);

        return redirect()->route('admin.bougies.index')
            ->with('success', "Bougie '{$bougie->nom}' créée avec succès.");
    }

    /**
     * Affiche les détails d'une bougie
     */
    public function show(Bougie $bougie)
    {
        return view('admin.bougies.show', compact('bougie'));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Bougie $bougie)
    {
        return view('admin.bougies.edit', [
            'bougie' => $bougie,
            'formats' => Bougie::formats(),
            'collections' => Bougie::collections(),
            'typesCire' => Bougie::typesCire(),
        ]);
    }

    /**
     * Met à jour une bougie
     */
    public function update(Request $request, Bougie $bougie)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:50|unique:bougies,reference,' . $bougie->id,
            'parfum' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'collection' => 'nullable|string|max:255',
            'format' => 'nullable|string|max:50',
            'type_cire' => 'nullable|string|max:50',
            'temps_brulure' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
            'quantite' => 'nullable|integer|min:0',
            'seuil_alerte' => 'nullable|integer|min:0',
        ]);

        $validated['quantite'] = $validated['quantite'] ?? $bougie->quantite;
        $validated['seuil_alerte'] = $validated['seuil_alerte'] ?? $bougie->seuil_alerte ?? Bougie::SEUIL_ALERTE_PAR_DEFAUT;

        $bougie->update($validated);

        return redirect()->route('admin.bougies.index')
            ->with('success', "Bougie '{$bougie->nom}' mise à jour.");
    }

    /**
     * Supprime une bougie
     */
    public function destroy(Bougie $bougie)
    {
        $nom = $bougie->nom;
        $bougie->delete();

        return redirect()->route('admin.bougies.index')
            ->with('success', "Bougie '{$nom}' supprimée.");
    }
}
