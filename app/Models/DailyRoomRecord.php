<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRoomRecord extends Model
{
    protected $fillable = ['record_date', 'room_id', 'time_slot_id', 'therapist_id', 'user_id', 'service_type', 'price'];
    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

}
