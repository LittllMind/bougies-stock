<?php

namespace App\Http\Controllers;

use App\Models\Fond;
use Illuminate\Http\Request;

class FondController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $fonds = Fond::orderBy('type')->get();

        return view('fonds.index', compact('fonds'));
    }

    public function update(Request $request, Fond $fond)
    {
        $validated = $request->validate([
            'quantite' => 'required|integer|min:0',
        ]);

        $fond->update([
            'quantite' => $validated['quantite'],
        ]);

        return redirect()
            ->route('fonds.index')
            ->with('success', 'Stock de fonds mis à jour.');
    }
}
