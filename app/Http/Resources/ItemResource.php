<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Category;

class ItemResource extends JsonResource
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
          "id"=>$this->id,
          "name"=> $this->name,
          "description"=> $this->description,
          "sale_price"=> $this->sale_price,
          "purchase_price"=> $this->purchase_price,
          "quantity"=> $this->quantity,
          "unit"=> is_null($this->unit_id) ? null : Unit::findOrFail($this->unit_id)->name,
          "category"=> is_null($this->category_id) ? null : Category::findOrFail($this->category_id)->name,
          "tax"=> is_null($this->tax_id) ? null : Tax::findOrFail($this->tax_id)->name,
          "enabled"=> $this->enabled == 1,
          "deleted_at"=> $this->deleted_at,
          "created_at"=> $this->created_at,
          "updated_at"=> $this->updated_at
        ];
        // return parent::toArray($request);
    }
}
