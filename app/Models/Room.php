<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    //
    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function dailyRoomRecords()
    {
        return $this->hasMany(DailyRoomRecord::class);
    }

}
