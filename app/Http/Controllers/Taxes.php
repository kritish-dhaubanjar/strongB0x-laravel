<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tax;
use App\Http\Resources\TaxResource;

class Taxes extends Controller
{
    public function index()
    {  
        return TaxResource::collection(Tax::all());
    }

    public function show($id)
    {
        return Tax::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'rate'=>'required'
        ]);

        $tax = Tax::create([
            'name' => $request->name,
            'rate' => $request->rate,
            'enabled'=>$request->enabled
        ]);    
        return new TaxResource($tax);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
            'rate' => 'required',
        ]);
        if(Tax::findOrFail($id)->update([
            'name' => $request->name,
            'rate' => $request->rate,
            'enabled'=>$request->enabled
        ]))
        return new TaxResource(Tax::findOrFail($id));
    }

    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);
        if(count($tax->items) > 0){
            return ['status' => 412];
        }
        if(Tax::findOrFail($id)->delete()){
            return $tax;
        }
    } 
}
