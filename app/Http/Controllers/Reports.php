<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Transaction;
use App\Models\Purchases\Bill;
use App\Models\Sales\Invoice;
use App\Models\Category;
use App\Models\Account;
use App\Http\Traits\DateTrait;

class Reports extends Controller
{
	use DateTrait;

	private $range;
    
    public function profitLoss($year){
    	$this->range = $this->getFiscalYear($year);

    	$payments = Transaction::where('type', 'expense')
        ->whereNull('document_id')
        ->whereNotNull('contact_id')
        ->where('paid_year', '>=', $this->range['earliest']['year'])
        ->where('paid_year', '<=', $this->range['latest']['year'])
        ->orderBy('paid_year', 'ASC')
        ->orderBy('paid_month', 'ASC')
        ->orderBy('paid_day', 'ASC')
        ->get(['paid_year AS year','paid_month AS month','paid_day AS day','amount', 'category_id'])->groupBy(['category_id','year','month']);



        $revenues = Transaction::where('type', 'income')
        ->whereNull('document_id')
        // ->whereNotNull('contact_id')
        ->where('paid_year', '>=', $this->range['earliest']['year'])
        ->where('paid_year', '<=', $this->range['latest']['year'])
        ->orderBy('paid_year', 'ASC')
        ->orderBy('paid_month', 'ASC')
        ->orderBy('paid_day', 'ASC')
        ->get(['paid_year AS year','paid_month AS month','paid_day AS day','amount', 'category_id'])->groupBy(['category_id','year','month']);

        $expenses = Transaction::where('type', 'expense')
        ->whereNull('document_id')
        ->whereNull('contact_id')
        ->where('paid_year', '>=', $this->range['earliest']['year'])
        ->where('paid_year', '<=', $this->range['latest']['year'])
        ->orderBy('paid_year', 'ASC')
        ->orderBy('paid_month', 'ASC')
        ->orderBy('paid_day', 'ASC')
        ->get(['paid_year AS year','paid_month AS month','paid_day AS day','amount', 'category_id'])->groupBy(['category_id','year','month']);


        $total_payment = $this->filterGroupTransaction($payments);
        $total_revenue = $this->filterGroupTransaction($revenues);
        $total_expense = $this->filterGroupTransaction($expenses);




        // $revenues = Transaction::where('type', 'income')
        // ->whereNull('document_id')
        // // ->whereNotNull('contact_id')
        // ->where('paid_year', '>=', $range['earliest']['year'])
        // ->where('paid_year', '<=', $range['latest']['year'])
        // ->orderBy('paid_year', 'ASC')
        // ->orderBy('paid_month', 'ASC')
        // ->orderBy('paid_day', 'ASC')
        // ->get(['paid_year AS year','paid_month AS month','paid_day AS day','amount', 'category_id'])->groupBy(['category_id','year','month']);


  //       $total_revenue['data'] = [];
  //       $total_revenue['amount'] = 0;

  //       foreach($revenues as $revenue){
  //           if($revenue->serial<=$range['latest']['serial'] && $revenue->serial>=$range['earliest']['serial']){
  //               $total_revenue['data'][] = $revenue;
  //               $total_revenue['amount'] += $revenue->amount;
  //           }
  //       }

  //       $expenses = Transaction::where('type', 'expense')
  //       ->whereNull('document_id')
  //       ->whereNull('contact_id')
  //       ->where('paid_year', '>=', $range['earliest']['year'])
  //       ->where('paid_year', '<=', $range['latest']['year'])
  //       ->orderBy('paid_year', 'ASC')
  //       ->orderBy('paid_month', 'ASC')
  //       ->orderBy('paid_day', 'ASC')
  //       ->get(['paid_year AS year','paid_month AS month','paid_day AS day','amount']);

  //       $total_expense['data'] = [];
  //       $total_expense['amount'] = 0;

  //       foreach($expenses as $expense){
  //           if($expense->serial<=$range['latest']['serial'] && $expense->serial>=$range['earliest']['serial']){
  //               $total_expense['data'][] = $expense;
  //               $total_expense['amount'] += $expense->amount;
  //           }
  //       }

		// //By Month		 
		// $total_payment['data'] = $this->aggregateMonthlyAmount($total_payment['data']);
  //       $total_revenue['data'] = $this->aggregateMonthlyAmount($total_revenue['data']);
  //       $total_expense['data'] = $this->aggregateMonthlyAmount($total_expense['data']);

        return [
        	'range'=>$this->range,
        	'revenues'=>$total_revenue,
        	'payments'=>$total_payment,
        	'expenses'=>$total_expense
        ];
    }

    private function aggregateMonthlyAmount($array){
    	$var = null;
        foreach($array as $data){
        	$var[$data['year']][(int)$data['month']]['data'][] = $data; 
        	if(array_key_exists('amount', $var[$data['year']][(int)$data['month']])){
        		$var[$data['year']][(int)$data['month']]['amount'] +=$data['amount'];
        	}else{
        		$var[$data['year']][(int)$data['month']]['amount'] = $data['amount'];
        	}
        }
        return $var;
    }


    private function filterGroupTransaction($_transactions_){

    	$total_transaction = [];

    	foreach($_transactions_ as $category_id=>$fiscal_transactions){
        	foreach ($fiscal_transactions as $transaction_year => $transactions_month) {
        		foreach ($transactions_month as $transaction_month => $transactions) {
        			foreach ($transactions as $transaction) {
        				if($transaction->serial<=$this->range['latest']['serial'] && $transaction->serial>=$this->range['earliest']['serial']){
        				$category = Category::findOrFail($category_id)->name;
        				// return $category;
                		$total_transaction[$category][$transaction_year][(int)$transaction_month]['data'][] = $transaction;
                		if(array_key_exists('amount', $total_transaction[$category][$transaction_year][(int)$transaction_month])){
                			$total_transaction[$category][$transaction_year][(int)$transaction_month]['amount'] += $transaction->amount;
                		}else{
                			$total_transaction[$category][$transaction_year][(int)$transaction_month]['amount'] = $transaction->amount;
                		}
        			}

            		}
        		}
        	}
        }
        return $total_transaction;
    }

    public function ledger($id){
    	$contact = Contact::findOrFail($id);

    	if($contact->type == 'customer'){
    		$invoices = Invoice::with('category:id,name')->where('customer_id', $id)
    		->orderBy('invoiced_year','DESC')->orderBy('invoiced_month','DESC')
    		->orderBy('invoiced_day','DESC')
    		->get(['invoice_number','amount','invoiced_year AS year','invoiced_month AS month','invoiced_day AS day','category_id']);

    		$contact->invoices = $invoices;
    	}else{
	    	$bills = Bill::with('category:id,name')
	    	->where('vendor_id', $id)
	    	->orderBy('billed_year','DESC')
	    	->orderBy('billed_month','DESC')
	    	->orderBy('billed_day','DESC')
	    	->get(['bill_number', 'amount', 'billed_year AS year', 'billed_month AS month', 'billed_day AS day', 'category_id']);

	    	$contact->bills = $bills;
    	}

    	$contact->transactions = Transaction::with(['account:id,name','category:id,name'])
    	->orderBy('paid_year','DESC')
	    ->orderBy('paid_month','DESC')
	    ->orderBy('paid_day','DESC')
	    ->where('contact_id', $id)
		->get(['amount', 'paid_year AS year', 'paid_month AS month', 'paid_day AS day', 'category_id', 'account_id']);
		
    	return $contact;
    }
}
