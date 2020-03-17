<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Http\Resources\AccountResource;

class Accounts extends Controller
{
    public function index()
    {  
        return AccountResource::collection(Account::all());
    }

    public function show($id)
    {
        return new AccountResource(Account::findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
        ]);

        $account = Account::create([
            'name' => $request->name,
            'number' => $request->number,
            'opening_balance' => $request->opening_balance,
            'bank_name' => $request->bank_name,
            'bank_phone' => $request->bank_phone,
            'bank_address' => $request->bank_address,
            'enabled' => $request->enabled,
        ]);    
        return new AccountResource($account);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
        ]);
        if(Account::findOrFail($id)->update([
            'name' => $request->name,
            'number' => $request->number,
            'opening_balance' => $request->opening_balance,
            'bank_name' => $request->bank_name,
            'bank_phone' => $request->bank_phone,
            'bank_address' => $request->bank_address,
            'enabled' => $request->enabled,
        ]))
        return new AccountResource(Account::findOrFail($id));
    }

    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        if(count($account->transactions) > 0){
            return ['status' => 412];
        }
        if(Account::findOrFail($id)->delete()){
            return $account;
        }
    } 
}
