<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Invoice;
use App\Models\Sales\InvoiceItem;
use App\Models\Sales\InvoiceTotal;
use App\Models\Tax;
use App\Http\Resources\InvoiceResource;
use Illuminate\Support\Facades\DB;

class Invoices extends Controller
{

    public function index(){
        return InvoiceResource::collection(Invoice::orderBy('invoiced_year', 'DESC')->orderBy('invoiced_month', 'DESC')->orderBy('invoiced_day', 'DESC')->get());
    }

    public function show($id){
        return new InvoiceResource(Invoice::findOrFail($id));
    }

    public function store(Request $request){
        $request->validate([
            'customer_id' => 'required',
            'invoiced_at' => 'required',
            'invoice_number'=> 'required',
            'items' => 'present|array|min:1',
            'items.*.id' => 'required',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.price' => 'required|numeric|gt:0'
        ]);

        DB::beginTransaction();
        
        $invoiced_at = explode("-", $request->invoiced_at);
        $due_at = is_null($request->due_at) ? null : explode("-", $request->due_at);

        try{
            $invoice = Invoice::create([
                'invoice_number'=> $request->invoice_number,
                'order_number'=>$request->order_number,
                'status'=>'sent',
                // 'invoiced_at'=>$request->invoiced_at,
                // 'due_at'=>$request->due_at,
                'invoiced_year' => $invoiced_at[0],
                'invoiced_month' => $invoiced_at[1],
                'invoiced_day' => $invoiced_at[2],
                'due_year' => $due_at ? $due_at[0] : null,
                'due_month' => $due_at ? $due_at[1] : null,
                'due_day' => $due_at ? $due_at[2] : null,
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

                if(!is_null($item['tax_id'])){
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

            if($request->has('discount') && !is_null($request->discount) && $request->discount > 0){
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

            if($request->has('tax_id') && !is_null($request->tax_id)){
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
        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }

        Db::commit();
        return new InvoiceResource($invoice);
    }

    public function update(Request $request, $id){
        $request->validate([
            'customer_id' => 'required',
            'invoiced_at' => 'required',
            'invoice_number'=> 'required',
            'items' => 'present|array|min:1',
            // id != item_id
            'items.*.item_id' => 'required',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.price' => 'required|numeric|gt:0'
        ]);

        DB::beginTransaction();
        
        $invoiced_at = explode("-", $request->invoiced_at);
        $due_at = is_null($request->due_at) ? null : explode("-", $request->due_at);

        try{
            $invoice = Invoice::create([
                'invoice_number'=> $request->invoice_number,
                'order_number'=>$request->order_number,
                'status'=>'sent',
                // 'invoiced_at'=>$request->invoiced_at,
                // 'due_at'=>$request->due_at,
                'invoiced_year' => $invoiced_at[0],
                'invoiced_month' => $invoiced_at[1],
                'invoiced_day' => $invoiced_at[2],
                'due_year' => $due_at[0],
                'due_month' => $due_at[1],
                'due_day' => $due_at[2],
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

                if(!is_null($item['tax_id'])){
                    $tax_id = $item['tax_id'];
                    $tax = Tax::findOrFail($item['tax_id']);
                    $tax_amount = ($tax->rate / 100) * ($total);
                }

                $amount_before_discount = $amount_before_discount + $total + $tax_amount;


                $invoice->items()->save(
                    new InvoiceItem([
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
            $invoice->totals()->save(
                new InvoiceTotal([
                    'code' => 'sub_total',
                    'amount' => $amount_before_discount
                    ])
            );

            /* discount */
            $taxable_amount = $amount_before_discount;

            if($request->has('discount') && !is_null($request->discount) && $request->discount > 0){
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

            if($request->has('tax_id') && !is_null($request->tax_id)){
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
            Invoice::findOrFail($id)->delete();

        }catch(Exception $e){
            DB::rollBack();
            throw $e;
        }

        Db::commit();
        return new InvoiceResource($invoice);
    }

    public function destroy($id){
        $invoice = Invoice::findOrFail($id);
        if($invoice->delete()){
            return new InvoiceResource($invoice);
        }
    }

}
