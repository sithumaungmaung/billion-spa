<?php

use App\Http\Controllers\BranchSelectionController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::middleware('auth')->group(function () {
      Route::get('/select-branch', [BranchSelectionController::class, 'index'])
        ->name('branch.selection');

    Route::post('/select-branch/{branch}', [BranchSelectionController::class, 'select'])
        ->name('branch.select');
});


Route::get('/invoice/preview/{ids}/{buy}/{free}/checkBill', [InvoiceController::class, 'previewInvoice'])->name('invoice.preview');
Route::get('/invoice/detail/{invoice_id}/preview', [InvoiceController::class, 'detailPDF'])->name('invoice.detail.pdf');
