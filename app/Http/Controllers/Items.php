<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Http\Resources\ItemResource;

class Items extends Controller
{
    public function index()
    {  
        return ItemResource::collection(Item::orderBy('created_at', 'DESC')->get());
        // return Item::paginate(25);
    }

    public function show($id)
    {
        return Item::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'sale_price' => 'required',
            'purchase_price'=> 'required'
        ]);

        $item = Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'sale_price' => $request->sale_price,
            'purchase_price' => $request->purchase_price,
            'quantity' => $request->quantity,
            'unit_id' => $request->unit_id,
            'category_id' => $request->category_id,
            'tax_id' => $request->tax_id,
            'enabled' => $request->enabled,
        ]);    

        return new ItemResource($item);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
            'sale_price' => 'required',
            'purchase_price'=> 'required'
        ]);
        if(Item::findOrFail($id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'sale_price' => $request->sale_price,
            'purchase_price' => $request->purchase_price,
            'quantity' => $request->quantity,
            'unit_id' => $request->unit_id,
            'category_id' => $request->category_id,
            'tax_id' => $request->tax_id,
            'enabled' => $request->enabled,
        ])
        ){
            return new ItemResource(Item::findOrFail($id));
        }
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        if(count($item->bills) > 0){
            return ['status' => 412];
        }
        if(Item::findOrFail($id)->delete()){
            return $item;
        }
    } 
}
