<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRoomRecord extends Model
{
    protected $fillable = ['record_date', 'room_id', 'time_slot_id', 'therapist_id', 'user_id', 'service_type'];
    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }
}