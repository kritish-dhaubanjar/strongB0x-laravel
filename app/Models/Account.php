<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'number', 'opening_balance', 'bank_name', 'bank_phone', 'bank_address', 'enabled'];

    public function transactions(){
    	return $this->hasMany('App\Models\Transaction');
    }
}
