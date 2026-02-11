<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/invoice/preview/{ids}/{buy}/{free}/checkBill', [InvoiceController::class, 'previewInvoice'])->name('invoice.preview');
Route::get('/invoice/detail/{invoice_id}/preview', [InvoiceController::class, 'detailPDF'])->name('invoice.detail.pdf');
