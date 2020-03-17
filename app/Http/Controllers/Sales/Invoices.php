<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Invoice;
use App\Models\Sales\InvoiceItem;
use App\Models\Sales\InvoiceTotal;
use App\Models\Tax;

class Invoices extends Controller
{

    public function index(){
        return Invoice::paginate(25);
    }

    public function show($id){
        return Invoice::with(['items', 'totals', 'contact', 'category', 'tax'])->findOrFail($id);
    }

    public function store(Request $request){
        $request->validate([
            'customer_id' => 'required',
            'invoice_date' => 'required',
            'invoice_number'=> 'required',
            'items' => 'present|array',
            'items.*.id' => 'required',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.price' => 'required|numeric|gt:0'
        ]);

        
        $invoice = Invoice::create([
            'invoice_number'=> $request->invoice_number,
            'order_number'=>$request->order_number,
            'status'=>'sent',
            'invoiced_at'=>$request->invoice_date,
            'due_at'=>$request->due_date,
            'amount'=> 0,
            'tax_id'=> $request->tax_id,
            'category_id'=>$request->category_id,
            'customer_id'=>$request->customer_id,
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


            $invoice->items()->save(
                new InvoiceItem([
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
        $invoice->totals()->save(
            new InvoiceTotal([
                'code' => 'sub_total',
                'amount' => $amount_before_discount
                ])
        );

        /* discount */
        $taxable_amount = $amount_before_discount;

        if($request->has('discount')){
            $invoice->totals()->save(
                new InvoiceTotal([
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
            $invoice->totals()->save(
                new InvoiceTotal([
                    'code' => 'tax',
                    'amount' => $tax_amount
                    ])
            );
        }

        $invoice->update(['amount'=> $taxable_amount + $tax_amount]);
        return ['status'=>200];
    }

    public function update(Request $request, $id){
        $request->validate([
            'customer_id' => 'required',
            'invoice_date' => 'required',
            'invoice_number'=> 'required',
            'items' => 'present|array',
            'items.*.id' => 'required',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.price' => 'required|numeric|gt:0'
        ]);
        $this->destroy($id);
        $this->store($request);
    }

    public function destroy($id){
        $invoice = Invoice::findOrFail($id);
        if($invoice->delete()){
            return ['status'=>200];
        }
    }

}
