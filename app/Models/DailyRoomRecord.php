<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DailyRoomRecord extends Model
{
    protected $fillable = ['record_date', 'room_id', 'start_time', 'end_time', 'therapist_id', 'user_id', 'service_type', 'room_price', 'service_type_price'];
    public function therapist()
    {
        return $this->belongsTo(Therapist::class);
    }

    public function scopeAvailableTherapists(Builder $query): Builder
    {
            $date = $date ?? now()->toDateString();
            $time = $time ?? now()->format('H:i:s');

            return $query->whereNotIn('id', function ($subQuery) use ($date, $time) {
                $subQuery->select('therapist_id')
                    ->from('daily_room_records')
                    ->where('record_date', $date)
                    ->where(function ($q) use ($time) {
                        $q->whereNull('start_time')
                        ->orWhere('start_time', '<=', $time);
                    })
                    ->where(function ($q) use ($time) {
                        $q->whereNull('end_time')
                        ->orWhere('end_time', '>=', $time);
                    });
            });
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeAvailableRooms(Builder $query): Builder
    {
        $now = now()->format('H:i:s');

        return $query->where('record_date', now()->toDateString())
            ->where(function ($q) use ($now) {
                $q->whereNull('start_time')
                  ->orWhere('start_time', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_time')
                  ->orWhere('end_time', '>=', $now);
            });
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

    // public function serviceType()
    // {
    //     return $this->hasOne(TherapistType::class, 'id', 'service_type');
    // }

    public function extraServices()
    {
        return $this->belongsToMany(ExtraService::class,'extra_service_sales','daily_room_record_id','extra_service_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }



    // Append
    protected $appends = ['total_time'];


    protected function getTotalTimeAttribute()
    {
        $startTime = Carbon::parse($this->start_time);
        $endTime = Carbon::parse($this->end_time);

        $totalMinutes = $startTime->diffInMinutes($endTime) / 60;
        // return round($totalMinutes);
        return ceil($totalMinutes);
    }


}