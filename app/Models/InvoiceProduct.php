<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $fillable = ['invoice_id', 'branch_id', 'product_id', 'product_name', 'product_code', 'quantity', 'unit_price', 'total_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
