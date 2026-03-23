<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcelExportList extends Model
{
    //
    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
