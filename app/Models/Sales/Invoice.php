<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = ['invoice_number', 'order_number', 'status', 'invoiced_at', 'due_at', 'amount', 'tax_id', 'category_id', 'customer_id', 'notes'];

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
}
