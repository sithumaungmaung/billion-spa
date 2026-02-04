<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Therapist extends Model
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

    // public function room/



}
