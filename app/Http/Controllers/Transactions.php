<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class Transactions extends Controller
{
    public function index(){
        return Transaction::paginate(25);
    }

    public function show($id){
        return Transaction::with(['contact', 'category', 'account'])->findOrFail($id);
    }

    public function revenues(){
        return Transaction::whereIn('category_id', [1,2])->paginate(25); //Deposit,Sales
    }

    public function payments(){
        return Transaction::where('category_id', 3)->paginate(25);   //Other
    }

    public function store(Request $request){
        $request->validate([
            'type' => 'required',
            'paid_at' => 'required',
            'amount'=> 'required',
            'account_id' => 'required',
            'category_id' => 'required',
            'payment_method' => 'required',
        ]);

        Transaction::create([
            'type' => $request->type,
            'paid_at' => $request->paid_at,
            'amount'=> $request->amount,
            'account_id' => $request->account_id,
            'document_id' => $request->document_id,
            'contact_id' => $request->contact_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'payment_method' => $request->payment_method,
        ]);

        return ['status'=>200];
    }

    public function update(Request $request, $id){
        $request->validate([
            'type' => 'required',
            'paid_at' => 'required',
            'amount'=> 'required',
            'account_id' => 'required',
            'category_id' => 'required',
            'payment_method' => 'required',
        ]);
        $this->store($request);
        $this->destroy($id);
        return ['status'=>200];
    }

    public function destroy($id){
        $transaction = Transaction::findOrFail($id);
        if($transaction->delete()){
            return ['status'=>200];
        }
    }


}
