<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = ['type', 'name', 'opening_balance', 'email', 'tax_number', 'phone', 'address', 'enabled'];

    public function bills(){
        return $this->hasMany('App\Models\Purchases\Bill', 'vendor_id');
    }

    public function invoices(){
        return $this->hasMany('App\Models\Sales\Invoice', 'customer_id');
    }

    public function transactions(){
        return $this->hasMany('App\Models\Transaction');
    }
}
