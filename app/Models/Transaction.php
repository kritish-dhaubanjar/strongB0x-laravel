<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Transaction extends Model
{
    use SoftDeletes;
    protected $fillable = ['type', 'paid_at', 'amount', 'account_id', 'document_id', 'contact_id', 'category_id', 'description', 'payment_method'];

    public function contact(){
        return $this->belongsTo('App\Models\Contact');
    }

    public function category(){
        return $this->belongsTo('App\Models\Category');
    }

    public function account(){
        return $this->belongsTo('App\Models\Account');
    }

    public function getPaidAtAttribute($value) {
        return Carbon::parse($value)->toDateString();
    }
}
