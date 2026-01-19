<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceRoom extends Model
{
    protected $fillable = ['invoice_id', 'branch_id', 'room_id', 'service_type', 'room_name', 'room_code', 'quantity', 'unit_price', 'total_price'];
}
