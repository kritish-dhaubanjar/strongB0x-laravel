<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchases\Bill;
use App\Models\Purchases\BillItem;
use App\Models\Purchases\BillTotal;
use App\Models\Tax;

class Bills extends Controller
{

    public function index(){
        return Bill::paginate(25);
    }

    public function show($id){
        return Bill::with(['items', 'totals', 'contact', 'category', 'tax'])->findOrFail($id);
    }

    public function store(Request $request){
        $request->validate([
            'vendor_id' => 'required',
            'bill_date' => 'required',
            'bill_number'=> 'required',
            'items' => 'present|array',
            'items.*.id' => 'required',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.price' => 'required|numeric|gt:0'
        ]);

        
        $bill = Bill::create([
            'bill_number'=> $request->bill_number,
            'order_number'=>$request->order_number,
            'status'=>'received',
            'billed_at'=>$request->bill_date,
            'due_at'=>$request->due_date,
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

            if(isset($item['tax_id'])){
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

        if($request->has('discount')){
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

        if($request->has('tax_id')){
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
        return ['status'=>200];
    }

    public function update(Request $request, $id){
        $request->validate([
            'vendor_id' => 'required',
            'bill_date' => 'required',
            'bill_number'=> 'required',
            'items' => 'present|array',
            'items.*.id' => 'required',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.price' => 'required|numeric|gt:0'
        ]);
        $this->destroy($id);
        $this->store($request);
    }

    public function destroy($id){
        $bill = Bill::findOrFail($id);
        if($bill->delete()){
            return ['status'=>200];
        }
    }

}
