<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

    protected $guarded = [];

    protected $table = 'invoices';


    public function invoiceRooms()
    {
        return $this->hasMany(InvoiceRoom::class);
    }

    public function invoiceProducts()
    {
        return $this->hasMany(InvoiceProduct::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invoiceExtraServices()
    {
        return $this->hasMany(InvoiceExtraService::class);
    }

}