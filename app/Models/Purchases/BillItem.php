<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['bill_id', 'item_id', 'name', 'quantity', 'total','price', 'tax_id', 'tax'];

    protected $casts = [
        'item_id' => 'integer',
        'tax_id' => 'integer'
    ];

    public function bill(){
        return $this->belongsTo('App\Models\Purchases\Bill');
    }

    public function item(){
        return $this->belongsTo('App\Models\Item');
    }

    public function tax(){
        return $this->belongsTo('App\Models\Tax');
    }


}
