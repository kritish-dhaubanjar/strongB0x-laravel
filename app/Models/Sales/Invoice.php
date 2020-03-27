<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = ['invoice_number', 'order_number', 'status', 'invoiced_year', 'invoiced_month','invoiced_day', 'due_year', 'due_month', 'due_day', 'amount', 'tax_id', 'category_id', 'customer_id', 'notes'];
    protected $appends = ['serial', 'date'];

    public function items(){
        return $this->hasMany('App\Models\Sales\InvoiceItem');
    }

    public function totals(){
        return $this->hasMany('App\Models\Sales\InvoiceTotal');
    }

    public function contact(){
        return $this->belongsTo('App\Models\Contact', 'customer_id');
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
