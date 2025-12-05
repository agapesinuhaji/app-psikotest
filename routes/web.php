<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::middleware(['auth'])->group(function () {

    # ----------- SPM ROUTE -----------
    Route::get('/spm', function () {
        return view('spm-start');
    })->name('spm.start');


    # ----------- PAPI-KOSTICK ROUTE -----------
    Route::get('/papikostick', function () {
        return view('papikostick-start');
    })->name('papikostick.start');


});




require __DIR__.'/auth.php';
