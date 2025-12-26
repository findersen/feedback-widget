<?php

use App\Http\Controllers\Admin\TicketAdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/widget', 'widget')->name('widget');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/tickets', [TicketAdminController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketAdminController::class, 'show'])->name('tickets.show');
    Route::patch('/tickets/{ticket}/status', [TicketAdminController::class, 'updateStatus'])->name('tickets.status');
});

require __DIR__.'/auth.php';
