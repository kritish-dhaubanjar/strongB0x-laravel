<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchases\Bill;
use App\Models\Purchases\BillItem;
use App\Models\Purchases\BillTotal;
use App\Models\Tax;
use App\Http\Resources\BillResource;
use Illuminate\Support\Facades\DB;

class Bills extends Controller
{

    public function index(){
        return BillResource::collection(Bill::orderBy('billed_year', 'DESC')->orderBy('billed_month', 'DESC')->orderBy('billed_day', 'DESC')->get());
    }

    public function show($id){
        return new BillResource(Bill::findOrFail($id));
    }

    public function store(Request $request){
        $request->validate([
            'vendor_id' => 'required',
            'billed_at' => 'required',
            'bill_number'=> 'required',
            'items' => 'present|array|min:1',
            'items.*.id' => 'required',
            // 'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric|gt:0'
        ]);

        DB::beginTransaction();
        
        $billed_at = explode("-", $request->billed_at);
        $due_at = is_null($request->due_at) ? null : explode("-", $request->due_at);

        try{
            $bill = Bill::create([
                'bill_number'=> $request->bill_number,
                'order_number'=>$request->order_number,
                'status'=>'received',
                // 'billed_at'=>$request->billed_at,
                // 'due_at'=>$request->due_at,
                'billed_year' => $billed_at[0],
                'billed_month' => $billed_at[1],
                'billed_day' => $billed_at[2],
                'due_year' => $due_at ? $due_at[0] : null,
                'due_month' => $due_at ? $due_at[1] : null,
                'due_day' => $due_at ? $due_at[2] : null,
                'amount'=> 0,
                'tax_id'=> $request->tax_id,
                'category_id'=>$request->category_id,
                'vendor_id'=>$request->vendor_id,
                'notes'=>$request->notes
            ]);

            $amount_before_discount = 0;

            foreach($request->items as $item){
                $tax_amount = 0;
                $total = $item['quantity'] * $item['price'];
                $tax_id = null;

                if(!is_null($item['tax_id'])){
                    $tax_id = $item['tax_id'];
                    $tax = Tax::findOrFail($item['tax_id']);
                    $tax_amount = ($tax->rate / 100) * ($total);
                }

                $amount_before_discount = $amount_before_discount + $total + $tax_amount;


                $bill->items()->save(
                    new BillItem([
                        'item_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $total,
                        'tax_id' => $tax_id,
                        'tax' => $tax_amount
                        ])
                );
            }


            /* sub_total */
            $bill->totals()->save(
                new BillTotal([
                    'code' => 'sub_total',
                    'amount' => $amount_before_discount
                    ])
            );

            /* discount */
            $taxable_amount = $amount_before_discount;

            if($request->has('discount') && !is_null($request->discount) && $request->discount > 0){
                $bill->totals()->save(
                    new BillTotal([
                        'code' => 'discount',
                        'amount' => $request->discount
                        ])
                );
                $taxable_amount -= $request->discount;
            }

            /* tax */
            $tax_amount = 0;

            if($request->has('tax_id') && !is_null($request->tax_id)){
                $tax = Tax::findOrFail($request->tax_id);
                $tax_amount = ($tax->rate/100) * $taxable_amount;
                $bill->totals()->save(
                    new BillTotal([
                        'code' => 'tax',
                        'amount' => $tax_amount
                        ])
                );
            }

            $bill->update(['amount'=> $taxable_amount + $tax_amount]);
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }

        Db::commit();
        return new BillResource($bill);
    }

    public function update(Request $request, $id){
        $request->validate([
            'vendor_id' => 'required',
            'billed_at' => 'required',
            'bill_number'=> 'required',
            'items' => 'present|array|min:1',
            // id != item_id
            'items.*.item_id' => 'required',
            // 'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric|gt:0'
        ]);

        DB::beginTransaction();
        
        $billed_at = explode("-", $request->billed_at);
        $due_at = is_null($request->due_at) ? null : explode("-", $request->due_at);

        try{
            $bill = Bill::create([
                'bill_number'=> $request->bill_number,
                'order_number'=>$request->order_number,
                'status'=>'received',
                // 'billed_at'=>$request->billed_at,
                // 'due_at'=>$request->due_at,
                'billed_year' => $billed_at[0],
                'billed_month' => $billed_at[1],
                'billed_day' => $billed_at[2],
                'due_year' => $due_at ? $due_at[0] : null,
                'due_month' => $due_at ? $due_at[1] : null,
                'due_day' => $due_at ? $due_at[2] : null,
                'amount'=> 0,
                'tax_id'=> $request->tax_id,
                'category_id'=>$request->category_id,
                'vendor_id'=>$request->vendor_id,
                'notes'=>$request->notes
            ]);

            $amount_before_discount = 0;

            foreach($request->items as $item){
                $tax_amount = 0;
                $total = $item['quantity'] * $item['price'];
                $tax_id = null;

                if(!is_null($item['tax_id'])){
                    $tax_id = $item['tax_id'];
                    $tax = Tax::findOrFail($item['tax_id']);
                    $tax_amount = ($tax->rate / 100) * ($total);
                }

                $amount_before_discount = $amount_before_discount + $total + $tax_amount;


                $bill->items()->save(
                    new BillItem([
                        // id != item_id
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'total' => $total,
                        'tax_id' => $tax_id,
                        'tax' => $tax_amount
                        ])
                );
            }


            /* sub_total */
            $bill->totals()->save(
                new BillTotal([
                    'code' => 'sub_total',
                    'amount' => $amount_before_discount
                    ])
            );

            /* discount */
            $taxable_amount = $amount_before_discount;

            if($request->has('discount') && !is_null($request->discount) && $request->discount > 0){
                $bill->totals()->save(
                    new BillTotal([
                        'code' => 'discount',
                        'amount' => $request->discount
                        ])
                );
                $taxable_amount -= $request->discount;
            }

            /* tax */
            $tax_amount = 0;

            if($request->has('tax_id') && !is_null($request->tax_id)){
                $tax = Tax::findOrFail($request->tax_id);
                $tax_amount = ($tax->rate/100) * $taxable_amount;
                $bill->totals()->save(
                    new BillTotal([
                        'code' => 'tax',
                        'amount' => $tax_amount
                        ])
                );
            }

            $bill->update(['amount'=> $taxable_amount + $tax_amount]);
            Bill::findOrFail($id)->delete();

        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }

        Db::commit();
        return new BillResource($bill);
    }

    public function destroy($id){
        $bill = Bill::findOrFail($id);
        if($bill->delete()){
            return new BillResource($bill);
        }
    }

}
