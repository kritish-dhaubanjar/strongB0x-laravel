<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Contact;

class BillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $discount = "0.00";

        foreach ($this->totals as $key => $value) {
            if($value->code == 'discount'){
                $discount = $value->amount;
                break;
            }
        }

        return [
            'id'=>$this->id,
            'amount'=>$this->amount,
            'vendor_id'=>(int)$this->vendor_id,
            'vendor'=>Contact::findOrFail($this->vendor_id)->name,
            'category_id'=>(int)$this->category_id,
            'billed_at'=>$this->billed_year.'-'.$this->billed_month.'-'.$this->billed_day,
            'due_at'=>is_null($this->due_year) ? null : $this->due_year.'-'.$this->due_month.'-'.$this->due_day,
            'bill_number'=>$this->bill_number,
            'order_number'=>$this->order_number,
            'items'=>$this->items,
            'discount'=>$discount,
            'tax_id'=> is_null($this->tax_id) ? null : (int) $this->tax_id,
            'notes'=>$this->notes,
        ];
    }
}
