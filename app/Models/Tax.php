<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'rate', 'enabled'];

    public function items(){
    	return $this->hasMany('App\Models\Item');
    }
}
