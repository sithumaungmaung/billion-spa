<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/invoice/preview/{ids}/{onePlusOne}/preview', [InvoiceController::class, 'previewInvoice'])->name('invoice.preview');
Route::get('/invoice/detail/{invoice_id}/invoice-detail', [InvoiceController::class, 'detailPDF'])->name('invoice.detail.pdf');