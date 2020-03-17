<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class Categories extends Controller
{
    public function index()
    {  
        return CategoryResource::collection(Category::all());
    }

    public function show($id)
    {
        return new CategoryResource(Category::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'type'=>'required'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'enabled' => $request->enabled,
        ]);    
        return new CategoryResource($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
            'type' =>'required',
        ]);
        if(Category::findOrFail($id)->update([
            'name' => $request->name,
            'type' => $request->type,
            'enabled' => $request->enabled,
        ]))
        return new CategoryResource(Category::findOrFail($id));
    }

    public function destroy($id)
    {
        // if(Category::findOrFail($id)->delete()){
        //     return ['status'=>200];
        // }
        $category = Category::findOrFail($id);
        if(count($category->items) > 0 || count($category->transactions) > 0 || count($category->bills) > 0 || count($category->invoices) > 0){
            return ['status' => 412];
        }
        if(Category::findOrFail($id)->delete()){
            return $category;
        }
    } 
}
