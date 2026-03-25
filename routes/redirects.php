<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirection legacy vinyles → catalogue bougies
|--------------------------------------------------------------------------
*/

// Redirection permanente du kiosque vinyles vers le catalogue bougies
Route::get('/kiosque', function () {
    return redirect()->route('catalogue', [], 301);
})->name('kiosque');

// Redirection des routes vinyles legacy
Route::get('/vinyles', function () {
    return redirect()->route('catalogue', [], 301);
});