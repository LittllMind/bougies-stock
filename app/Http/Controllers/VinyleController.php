<?php

namespace App\Http\Controllers;

use App\Models\Vinyle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VinyleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('kiosque');
    }

    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $vinyles = Vinyle::query()
            ->when($search, function ($query, $search) {
                return $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('modele', 'like', "%{$search}%");
            })
            ->orderBy('nom')
            ->paginate(10);

        return view('vinyles.index', compact('vinyles', 'search'));
    }

    public function create()
    {
        return view('vinyles.form', ['vinyle' => new Vinyle()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:0',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $vinyle = Vinyle::create($validated);

        // Upload des photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $vinyle->addMedia($photo)
                    ->toMediaCollection('photos');
            }
        }

        return redirect()->route('vinyles.index')
            ->with('success', 'Vinyle ajouté avec succès');
    }

    public function edit(Vinyle $vinyle)
    {
        return view('vinyles.form', compact('vinyle'));
    }

    public function update(Request $request, Vinyle $vinyle)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'modele' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:0',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $vinyle->update($validated);

        // Upload de nouvelles photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $vinyle->addMedia($photo)
                    ->toMediaCollection('photos');
            }
        }

        // Suppression des photos sélectionnées
        if ($request->has('delete_photos')) {
            foreach ($request->delete_photos as $mediaId) {
                $vinyle->media()->find($mediaId)?->delete();
            }
        }

        return redirect()->route('vinyles.index')
            ->with('success', 'Vinyle modifié avec succès');
    }

    public function destroy(Vinyle $vinyle)
    {
        $vinyle->delete();

        return redirect()->route('vinyles.index')
            ->with('success', 'Vinyle supprimé avec succès');
    }

    public function kiosque()
    {
        $vinyles = Vinyle::with('media')->orderBy('nom')->get();

        $vinylesData = $vinyles->map(function ($v) {
            return [
                'id'       => $v->id,
                'nom'      => $v->nom,
                'modele'   => $v->modele,
                'prix'     => $v->prix,
                'quantite' => $v->quantite,
                'photo'    => $v->hasMedia('photos')
                    ? $v->getFirstMediaUrl('photos', 'medium')
                    : null,
            ];
        });

        return view('vinyles.kiosque', [
            'vinyles'      => $vinyles,      // si tu en as besoin dans le HTML
            'vinylesData'  => $vinylesData,  // pour Alpine/JS
        ]);
    }
}
