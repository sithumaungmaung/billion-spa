<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRoomRecord extends Model
{
    protected $fillable = ['record_date', 'room_id', 'time_slot_id', 'therapist_id', 'user_id', 'service_type', 'price', 'service_type_price'];
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

    public function saleProducts()
    {
        return $this->hasMany(ProductSale::class);
    }

    public function therapistType()
    {
        return $this->belongsTo(TherapistType::class, 'service_type');
    }

    public function serviceType()
    {
        return $this->hasOne(TherapistType::class, 'id', 'service_type');
    }

    public function extraServices()
    {
        return $this->belongsToMany(ExtraService::class,'extra_service_sales','daily_room_record_id','extra_service_id');
    }

}
