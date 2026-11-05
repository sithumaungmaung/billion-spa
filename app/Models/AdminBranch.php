<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminBranch extends Model
{
    //

    protected $guarded = [];


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    // public function admin()
    // {
    //     return $this->belongsTo(User::class, 'user_id')->where('role' , '!=', 'customer');
    // }

    // public function customer()
    // {
    //     return $this->belongsTo(User::class, 'user_id')->where('role' , 'customer');
    // }

}
