<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit; 
use App\Http\Resources\UnitResource;

class Units extends Controller
{
    public function index()
    {  
        return UnitResource::collection(Unit::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
        ]);

        $unit = Unit::create([
            'name' => $request->name,
            'enabled' => $request->enabled,
        ]);    
        return new UnitResource($unit);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
        ]);
        if(Unit::findOrFail($id)->update([
            'name' => $request->name,
            'enabled' => $request->enabled,
        ]))
        return new UnitResource(Unit::findOrFail($id));
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        if(count($unit->items) > 0){
            return ['status' => 412];
        }
        if(Unit::findOrFail($id)->delete()){
            return $unit;
        }
    }    
}
