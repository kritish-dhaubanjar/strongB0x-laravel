<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Bill extends Model
{
    use SoftDeletes;

    protected $fillable = ['bill_number', 'order_number', 'status', 'billed_year', 'billed_month','billed_day', 'due_year', 'due_month', 'due_day', 'amount', 'tax_id', 'category_id', 'vendor_id', 'notes'];
    protected $appends = ['serial', 'date'];

    protected $casts = [
        'amount' => 'float',
    ];

    public function items(){
        return $this->hasMany('App\Models\Purchases\BillItem');
    }

    public function totals(){
        return $this->hasMany('App\Models\Purchases\BillTotal');
    }

    public function contact(){
        return $this->belongsTo('App\Models\Contact', 'vendor_id');
    }

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }

    public function tax(){
        return $this->belongsTo('App\Models\Tax');
    }

    public function delete()
    {
        $this->items()->delete();
        $this->totals()->delete();
        return parent::delete();
    }

    //{"year": 2077, "month": "12", "day": "24", "serial": "20771224"}

    public function getSerialAttribute(){
        return $this->year.$this->month.$this->day;
    }

    public function getDateAttribute(){
        return "$this->year-$this->month-$this->day";
    }
}
