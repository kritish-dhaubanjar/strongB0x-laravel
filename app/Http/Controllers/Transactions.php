<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class Transactions extends Controller
{
    public function index(){
        return TransactionResource::collection(Transaction::orderBy('paid_year', 'DESC')->orderBy('paid_month', 'DESC')->orderBy('paid_day', 'DESC')->get());
    }

    public function show($id){
        return new TransactionResource(Transaction::findOrFail($id));
    }

    public function revenues(){
        return TransactionResource::collection(Transaction::where('type', 'income')->where('document_id', null)->get()); //Deposit,Sales
    }

    public function payments(){
        return TransactionResource::collection(Transaction::where('type', 'expense')->where('document_id', null)->get());   //Other
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

        $date = explode("-", $request->paid_at);

        $transaction = $transaction = Transaction::create([
            'type' => $request->type,
            'paid_year' => $date[0],
            'paid_month' => $date[1],
            'paid_day' => $date[2],
            'amount'=> $request->amount,
            'account_id' => $request->account_id,
            'document_id' => $request->document_id,
            'contact_id' => $request->contact_id,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'payment_method' => $request->payment_method,
        ]);

        return new TransactionResource($transaction);

    }

    public function update(Request $request, $id){
        DB::beginTransaction();
        try{             

            $request->validate([
                'type' => 'required',
                'paid_at' => 'required',
                'amount'=> 'required',
                'account_id' => 'required',
                'category_id' => 'required',
                'payment_method' => 'required',
            ]);

            $transaction = Transaction::findOrFail($id);
            $transaction->delete();  

            $date = explode("-", $request->paid_at);

            $transaction = Transaction::create([
                'type' => $request->type,
                'paid_year' => $date[0],
                'paid_month' => $date[1],
                'paid_day' => $date[2],
                'amount'=> $request->amount,
                'account_id' => $request->account_id,
                'document_id' => $request->document_id,
                'contact_id' => $request->contact_id,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'payment_method' => $request->payment_method,
            ]);
        }catch(Exception $e){
            throw $e;
            DB::rollBack();
        }

        DB::commit();

        return new TransactionResource($transaction);

    }

    public function destroy($id){
        $transaction = Transaction::findOrFail($id);
        if($transaction->delete()){
            return new TransactionResource($transaction);
        }
    }


}
