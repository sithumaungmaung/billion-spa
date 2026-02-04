<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraService extends Model
{
    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function extraServiceSales()
    {
        return $this->hasMany(ExtraServiceSale::class);
    }

}
