<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceTotal extends Model
{
    use SoftDeletes;

    protected $fillable = ['invoice_id', 'code', 'amount'];

    protected $casts = [
        'invoice_id' => 'integer'
    ];
    
    public function invoice(){
        return $this->belongsTo('App\Models\Sales\Invoice');
    }
}
