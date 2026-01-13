<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
    protected $fillable = ['daily_room_record_id', 'product_id', 'quantity', 'unit_price', 'total_price', 'branch_id'];
}
