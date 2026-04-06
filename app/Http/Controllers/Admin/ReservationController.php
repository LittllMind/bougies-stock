<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::orderBy('date', 'desc')->get();
        return view('admin.reservations', compact('reservations'));
    }

    public function create()
    {
        return view('admin.reservations-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_nom' => 'required|string',
            'client_telephone' => 'required|string',
            'client_email' => 'nullable|email',
            'date' => 'required|date',
            'heure' => 'required',
            'prestation' => 'required|in:express,essentiel,premium',
            'prix' => 'required|numeric',
        ]);

        Reservation::create(array_merge($validated, ['statut' => 'nouveau']));
        return redirect()->route('admin.reservations')->with('success', 'Réservation créée');
    }

    public function update(Request $request, Reservation $reservation)
    {
        $reservation->update(['statut' => $request->statut]);
        return back()->with('success', 'Statut mis à jour');
    }
}
