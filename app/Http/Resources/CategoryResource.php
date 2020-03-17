<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "type"=>$this->type,   
            "enabled"=> $this->enabled == 1,
        ];
        // return [
        //   "text"=> $this->name,
        //   "value"=>$this->id,
        //   "type"=>$this->type,
        //   "enabled"=> $this->enabled == 1,
        // ];
    }
}
