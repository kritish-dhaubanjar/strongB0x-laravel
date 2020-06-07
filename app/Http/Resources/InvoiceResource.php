<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Contact;

class InvoiceResource extends JsonResource
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
            'customer_id'=>$this->customer_id,
            'customer'=>Contact::findOrFail($this->customer_id)->name,
            'category_id'=>$this->category_id,
            'invoiced_at'=>$this->invoiced_year.'-'.$this->invoiced_month.'-'.$this->invoiced_day,
            'due_at'=>is_null($this->due_year) ? null : $this->due_year.'-'.$this->due_month.'-'.$this->due_day,
            'invoice_number'=>$this->invoice_number,
            'order_number'=>$this->order_number,
            'items'=>$this->items,
            'discount'=>$discount,
            'tax_id'=>$this->tax_id,
            'notes'=>$this->notes,
        ];
    }
}
