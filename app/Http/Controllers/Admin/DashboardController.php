<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $reservations = Reservation::orderBy('date', 'desc')->take(50)->get();
        return view('admin.dashboard', compact('reservations'));
    }

    public function calendar()
    {
        return view('admin.calendar');
    }
}
