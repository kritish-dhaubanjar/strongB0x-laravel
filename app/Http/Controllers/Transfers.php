<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\Transaction;
use App\Http\Resources\TransferResource;
use Illuminate\Support\Facades\DB;

class Transfers extends Controller
{
    public function index(){
        return TransferResource::collection(Transfer::orderBy('created_at', 'DESC')->get());
    }

    public function show($id){
        return new TransferResource(Transfer::findOrFail($id));
    }

    public function store(Request $request){
        $request->validate([
            'expense_account_id' => 'required',
            'income_account_id' => 'required',
            'paid_at' => 'required',
            'amount'=> 'required',
            'payment_method' => 'required'
        ]);

        DB::beginTransaction();

        try{
            $transfer = Transfer::create();

            $expense = new Transaction([
                'type' => 'expense',
                'paid_at' => $request->paid_at,
                'amount'=> $request->amount,
                'account_id' => $request->expense_account_id,
                'category_id' => 5,
                'description' => $request->description,
                'payment_method' => $request->payment_method,
            ]);

            $income = new Transaction([
                'type' => 'income',
                'paid_at' => $request->paid_at,
                'amount'=> $request->amount,
                'account_id' => $request->income_account_id,
                'category_id' => 5,
                'description' => $request->description,
                'payment_method' => $request->payment_method,
            ]);

            $transfer->transactions()->save($expense);
            $transfer->transactions()->save($income);

        }catch(Exception $e){
            DB:rollback();
            throw $e;
        }
            
        DB::commit();
        return new TransferResource($transfer);
        
    }

    public function update(Request $request, $id){

        $request->validate([
            'expense_account_id' => 'required',
            'income_account_id' => 'required',
            'paid_at' => 'required',
            'amount'=> 'required',
            'payment_method' => 'required'
        ]);

        DB::beginTransaction();

        try{
            $transfer = Transfer::findOrFail($id);

            $transfer->transactions()->delete();

            $expense = new Transaction([
                'type' => 'expense',
                'paid_at' => $request->paid_at,
                'amount'=> $request->amount,
                'account_id' => $request->expense_account_id,
                'category_id' => 5,
                'description' => $request->description,
                'payment_method' => $request->payment_method,
            ]);

            $income = new Transaction([
                'type' => 'income',
                'paid_at' => $request->paid_at,
                'amount'=> $request->amount,
                'account_id' => $request->income_account_id,
                'category_id' => 5,
                'description' => $request->description,
                'payment_method' => $request->payment_method,
            ]);

            $transfer->transactions()->save($expense);
            $transfer->transactions()->save($income);

        }catch(Exception $e){
            DB:rollback();
            throw $e;
        }
            
        DB::commit();
        return new TransferResource($transfer);
    }

    public function destroy($id){
        $transfer = Transfer::findOrFail($id);
        if($transfer->delete()){
            return $transfer;
        }
    }

}
