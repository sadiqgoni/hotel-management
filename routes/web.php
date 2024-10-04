<?php

use App\Filament\Pages\ReservationSummary;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderReportController;
use App\Filament\Management\Pages\OrderReport;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/reservations/{reservation}/print', [ReservationController::class, 'print'])->name('reservations.print');
Route::get('/invoice/generate/{order}', [InvoiceController::class, 'generateInvoice'])->name('invoice.generate');

// Route::get('/order-report/generate', [OrderReportController::class, 'generate'])->name('restaurant-report.generate');

Route::get('/order-report', OrderReportController::class)->name('order-report.generate');
Route::group(['prefix' => 'printer'], function () {
    Route::get('/', [PrinterController::class, 'index'])
        ->can('read printer')
        ->name('printer.index');

    Route::post('/', [PrinterController::class, 'store'])
        ->can('create printer')
        ->name('printer.store');

    Route::put('/{printer}', [PrinterController::class, 'update'])
        ->can('update printer')
        ->name('printer.update');

    Route::delete('/{printer}', [PrinterController::class, 'destroy'])
        ->can('delete printer')
        ->name('printer.destroy');
});
