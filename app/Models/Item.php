<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'sale_price', 'purchase_price', 'quantity', 'unit_id', 'category_id', 'tax_id', 'enabled'];

    protected $casts = [
        'tax_id' => 'int',
        'category_id' => 'int',
        'unit_id' => 'int'
    ];

    public function unit(){
        return $this->belongsTo('App\Models\Unit');
    }

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }

    public function tax(){
        return $this->belongsTo('App\Models\Tax');
    }

    public function bills(){
        return $this->hasMany('App\Models\Purchases\BillItem');
    }
}
