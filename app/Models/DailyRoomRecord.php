<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRoomRecord extends Model
{
    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }
}
