<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'type', 'enabled'];

    public function items(){
        return $this->hasMany('App\Models\Item');
    }

    public function bills(){
        return $this->hasMany('App\Models\Purchases\Bill');
    }

    public function invoices(){
        return $this->hasMany('App\Models\Sales\Invoice');
    }

    public function transactions(){
        return $this->hasMany('App\Models\Transaction');
    }
}
