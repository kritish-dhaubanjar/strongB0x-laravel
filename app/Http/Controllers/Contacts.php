<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Http\Resources\ContactResource;

class Contacts extends Controller
{
    public function index($type)
    {  
        if(in_array($type, ['vendor', 'customer'])){
            return ContactResource::collection(Contact::where('type', $type)->orderBy('created_at', 'DESC')->get());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'=>'required',
            'name'=>'required',
            'opening_balance'=>'required'
        ]);

        $contact = Contact::create([
            'type'=> $request->type,
            'name' => $request->name,
            'opening_balance' => $request->opening_balance,
            'email' => $request->email, 
            'tax_number' => $request->tax_number, 
            'phone' => $request->phone, 
            'address' => $request->address,
            'enabled' => $request->enabled
        ]);    
        return new ContactResource($contact);
    }

    public function show($id)
    {
        return Contact::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type'=>'required',
            'name'=>'required',
            'opening_balance'=>'required'
        ]);
        if(Contact::findOrFail($id)->update([
            'type'=> $request->type,
            'name' => $request->name,
            'opening_balance' => $request->opening_balance,
            'email' => $request->email, 
            'tax_number' => $request->tax_number, 
            'phone' => $request->phone, 
            'address' => $request->address,
            'enabled' => $request->enabled
        ]))
        return new ContactResource(Contact::findOrFail($id));
        
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        if(count($contact->bills) > 0 || count($contact->invoices) > 0){
            return ['status' => 412];
        }
        if(Contact::findOrFail($id)->delete()){
            return $contact;
        }
    }
}