<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use SoftDeletes;
    protected $fillable = ['expense_transaction_id', 'income_transaction_id'];

    public function transactions(){
        return $this->hasMany('App\Models\Transaction', 'document_id');
    }

    public function delete(){
        $this->transactions()->delete();
        return parent::delete();
    }
}
