<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/invoice/preview/{ids}/{onePlusOne}', [InvoiceController::class, 'previewPDF'])->name('invoice.preview');
