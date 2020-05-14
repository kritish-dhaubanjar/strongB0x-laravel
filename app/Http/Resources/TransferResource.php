<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Account;

class TransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->transactions[1]->type == 'income'){
            $income_id = $this->transactions[1]->account_id;
            $expense_id = $this->transactions[0]->account_id;
        }else{
            $income_id = $this->transactions[0]->account_id;
            $expense_id = $this->transactions[1]->account_id;
        }

        return [
            'id'=>$this->id,
            'paid_at'=>$this->transactions[0]->paid_year.'-'.$this->transactions[0]->paid_month.'-'.$this->transactions[0]->paid_day,
            'amount' => $this->transactions[0]->amount,
            'income_account_id' => (int)$income_id,
            'income' => Account::findOrFail($income_id)->name,
            'expense_account_id' => (int) $expense_id,
            'expense' => Account::findOrFail($expense_id)->name,
            'description' => $this->transactions[0]->description,
            'payment_method' => $this->transactions[0]->payment_method,
        ];
    }

}
