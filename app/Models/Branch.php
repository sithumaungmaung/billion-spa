<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    //
    protected $guarded = [];

    public function therapists()
    {
        return $this->hasMany(Therapist::class);
    }

}
