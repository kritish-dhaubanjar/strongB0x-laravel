<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return [
        //   "text"=> $this->name,
        //   "value"=>$this->id,
        //   "enabled"=> $this->enabled == 1,
        // ];
        return [
            "id" => $this->id,
            "name" => $this->name,
            "enabled"=> $this->enabled == 1,
        ];
        // return parent::toArray($request);
    }
}
