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
        $filter = $request->get('filter', null); // stock_bas / rupture / null

        $vinyles = Vinyle::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('modele', 'like', "%{$search}%");
                });
            })
            ->when($filter === 'stock_bas', function ($query) {
                // mêmes règles que dans StatsController
                $query->where('quantite', '>', 0)
                    ->where('quantite', '<=', 3);
            })
            ->when($filter === 'rupture', function ($query) {
                $query->where('quantite', '<=', 0);
            })
            ->orderBy('nom')
            ->paginate(10)
            ->appends($request->only('search', 'filter')); // pour garder les filtres en pagination

        return view('vinyles.index', compact('vinyles', 'search', 'filter'));
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
            'photo_standard' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'photo_miroir'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'photo_dore'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',

        ]);

        // STORE
        $vinyle = Vinyle::create($validated);

        if ($request->hasFile('photo_standard')) {
            $vinyle->addMediaFromRequest('photo_standard')
                ->toMediaCollection('photo_standard');
        }

        if ($request->hasFile('photo_miroir')) {
            $vinyle->addMediaFromRequest('photo_miroir')
                ->toMediaCollection('photo_miroir');
        }

        if ($request->hasFile('photo_dore')) {
            $vinyle->addMediaFromRequest('photo_dore')
                ->toMediaCollection('photo_dore');
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
        if ($request->hasFile('photo_standard')) {
            $vinyle->clearMediaCollection('photo_standard');
            $vinyle->addMediaFromRequest('photo_standard')
                ->toMediaCollection('photo_standard');
        }

        if ($request->hasFile('photo_miroir')) {
            $vinyle->clearMediaCollection('photo_miroir');
            $vinyle->addMediaFromRequest('photo_miroir')
                ->toMediaCollection('photo_miroir');
        }

        if ($request->hasFile('photo_dore')) {
            $vinyle->clearMediaCollection('photo_dore');
            $vinyle->addMediaFromRequest('photo_dore')
                ->toMediaCollection('photo_dore');
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
        $vinyles = Vinyle::orderBy('nom')->get();

        $vinylesData = $vinyles->map(function (Vinyle $vinyle) {
            return [
                'id'        => $vinyle->id,
                'nom'       => $vinyle->nom,
                'modele'    => $vinyle->modele,
                'prix'      => $vinyle->prix,
                'quantite'  => $vinyle->quantite,

                // Image "standard" : on essaie d'abord la nouvelle collection,
                // sinon on retombe sur l’ancienne 'photos' pour compatibilité.
                'image_standard' => $vinyle->getFirstMediaUrl('photo_standard', 'medium')
                    ?: $vinyle->getFirstMediaUrl('photos', 'medium'),

                'image_miroir'   => $vinyle->getFirstMediaUrl('photo_miroir', 'medium'),
                'image_dore'     => $vinyle->getFirstMediaUrl('photo_dore', 'medium'),
            ];
        });

        // On passe un tableau "pur" au Blade (pas une collection Laravel)
        return view('vinyles.kiosque', [
            'vinylesData' => $vinylesData->values()->all(),
        ]);
    }
}
