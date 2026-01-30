<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraServiceSale extends Model
{
    protected $guarded = [];

    public function extraService()
    {
        return $this->belongsTo(ExtraService::class);
    }

}
