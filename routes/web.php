<?php

use App\Filament\Pages\ReservationSummary;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/reservations/{reservation}/print', [ReservationController::class, 'print'])->name('reservations.print');

