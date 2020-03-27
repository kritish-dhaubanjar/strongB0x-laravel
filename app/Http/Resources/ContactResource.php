<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'type'=> $this->type,
            'name' => $this->name,
            'opening_balance' => $this->opening_balance,
            'email' => $this->email, 
            'tax_number' => $this->tax_number, 
            'phone' => $this->phone, 
            'address' => $this->address,
            'enabled' => $this->enabled == 1
        ];
    }
}
