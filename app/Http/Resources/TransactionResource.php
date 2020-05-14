<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Account;
use App\Models\Category;
use App\Models\Contact;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id'=>$this->id,
            'type'=>$this->type,
            'paid_at'=>$this->paid_year.'-'.$this->paid_month.'-'.$this->paid_day,
            'amount' => $this->amount,
            'account_id' => (int)$this->account_id,
            'account' => Account::findOrFail($this->account_id)->name,
            'contact_id' => (int)$this->contact_id,
            'contact_name' => is_null($this->contact_id) ? null : Contact::findOrFail($this->contact_id)->name,
            'category_id' => (int)$this->category_id,
            'category' => Category::findOrFail($this->category_id)->name,
            'description' => $this->description,
            'payment_method' => $this->payment_method,
        ];
    }
}
