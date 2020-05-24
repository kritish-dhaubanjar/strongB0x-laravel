<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Transaction extends Model
{
    use SoftDeletes;
    protected $fillable = ['type', 'paid_year','paid_month','paid_day' , 'amount', 'account_id', 'document_id', 'contact_id', 'category_id', 'description', 'payment_method'];

    protected $appends = ['serial','date'];

    protected $casts = [
        'amount' => 'float',
    ];

    public function contact(){
        return $this->belongsTo('App\Models\Contact');
    }

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }

    public function account(){
        return $this->belongsTo('App\Models\Account');
    }

    //{"year": 2077, "month": "12", "day": "24", "serial": "20771224"}

    public function getSerialAttribute(){
        return $this->year.$this->month.$this->day;
    }

    public function getDateAttribute(){
        return "$this->year-$this->month-$this->day";
    }

}
