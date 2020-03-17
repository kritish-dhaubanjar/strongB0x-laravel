<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'id'=> $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'opening_balance' => $this->opening_balance, 
            'bank_name' => $this->bank_name, 
            'bank_phone' => $this->bank_phone, 
            'bank_address' => $this->bank_address,
            'enabled' => $this->enabled == 1,
        ];
    }
}
