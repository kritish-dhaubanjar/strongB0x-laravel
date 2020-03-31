<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchases\BillItem;
use App\Models\Sales\InvoiceItem;

class Stocks extends Controller
{
    public function show($id){
    	$purchases = BillItem::with('bill:id,bill_number,billed_year AS year,billed_month AS month,billed_day AS day',
    		'tax:id,name')
    	->where('item_id', $id)
    	->get(['bill_id','quantity','price','total','tax_id','tax as tax_amount']);

    	$purchase_count = BillItem::where('item_id', $id)->pluck('quantity')->sum();

    	$sales = InvoiceItem::with('invoice:id,invoice_number,invoiced_year AS year,invoiced_month AS month,invoiced_day AS day',
    		'tax:id,name')
    	->where('item_id', $id)
    	->get(['invoice_id','quantity','price','total','tax_id','tax as tax_amount']);

    	$sales_count = InvoiceItem::where('item_id', $id)->pluck('quantity')->sum();

    	return [
    		'sales'=> $sales,
    		'purchases' => $purchases,
    		'sale_count'=> $sales_count,
    		'purchase_count'=> $purchase_count,
    	];
    }
}
