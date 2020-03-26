<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Purchases\Bill;
use App\Models\Sales\Invoice;

use App\Http\Traits\DateTrait;

class Statistics extends Controller
{
    use DateTrait;

    private $months = [
        "baishak",
        "jestha",
        "ashad",
        "shrawn",
        "bhadra",
        "sshwin",
        "kartik",
        "mangshir",
        "poush",
        "magh",
        "falgun",
        "chaitra"
      ];


    public function timeline($timeline){

        $monthly = false;
        $yearly = false;

        $range = null;
        $latest = $this->getActiveDate();

        switch($timeline){
            case 'last7days':
                $range = $this->getBeforeDate(7);
            break;
            case 'last28days':
                $range = $this->getBeforeDate(28);
            break;
            case 'last90days':
                $range = $this->getBeforeDate(90);
            break;
            case 'last365days':
                $range = $this->getBeforeDate(365);
                $monthly = true;
            break;
            case 'lifetime':
                $range = $this->getLifetime();
                $yearly = true;
            break;
            default:
                $year = substr($timeline, -4);
                $month = substr($timeline, 0, strlen($timeline) - 4);
                $month_index = array_search($month, $this->months);
                $range = $this->getMonthDate($year, $month_index);
            break;
        }

        // return $range;

        /*
        {
            "latest": {
                "year": 2077,
                "month": "12",
                "day": "24",
                "serial": "20771224"
            },
            "earliest": {
                "year": 2077,
                "month": "12",
                "day": "17",
                "serial": "20771217"
            }
        }
        */

        $bills = Bill::where('billed_year', '>=', $range['earliest']['year'])
        ->where('billed_year', '<=', $range['latest']['year'])
        ->orderBy('billed_year', 'ASC')->orderBy('billed_month', 'ASC')->orderBy('billed_day', 'ASC')->get(['billed_year AS year','billed_month AS month','billed_day AS day','amount']);

        $total_bill['data'] = [];
        $total_bill['amount'] = 0;

        foreach($bills as $bill){
            if($bill->serial<=$range['latest']['serial'] && $bill->serial>=$range['earliest']['serial']){
                $total_bill['data'][] = $bill;
                $total_bill['amount'] += $bill->amount;
            }
        }

        $invoices = Invoice::where('invoiced_year', '>=', $range['earliest']['year'])
        ->where('invoiced_year', '<=', $range['latest']['year'])
        ->orderBy('invoiced_year', 'ASC')->orderBy('invoiced_month', 'ASC')->orderBy('invoiced_day', 'ASC')->get(['invoiced_year AS year','invoiced_month AS month','invoiced_day AS day','amount']);

        $total_invoice['data'] = [];
        $total_invoice['amount'] = 0;

        foreach($invoices as $invoice){
            if($invoice->serial<=$range['latest']['serial'] && $invoice->serial>=$range['earliest']['serial']){
                $total_invoice['data'][] = $invoice;
                $total_invoice['amount'] += $invoice->amount;
            }
        }

        $payments = Transaction::where('type', 'expense')
        ->whereNull('document_id')
        ->whereNotNull('contact_id')
        ->where('paid_year', '>=', $range['earliest']['year'])
        ->where('paid_year', '<=', $range['latest']['year'])
        ->orderBy('paid_year', 'ASC')
        ->orderBy('paid_month', 'ASC')
        ->orderBy('paid_day', 'ASC')
        ->get(['paid_year AS year','paid_month AS month','paid_day AS day','amount']);

        $total_payment['data'] = [];
        $total_payment['amount'] = 0;

        foreach($payments as $payment){
            if($payment->serial<=$range['latest']['serial'] && $payment->serial>=$range['earliest']['serial']){
                $total_payment['data'][] = $payment;
                $total_payment['amount'] += $payment->amount;
            }
        }

        $revenues = Transaction::where('type', 'income')
        ->whereNull('document_id')
        // ->whereNotNull('contact_id')
        ->where('paid_year', '>=', $range['earliest']['year'])
        ->where('paid_year', '<=', $range['latest']['year'])
        ->orderBy('paid_year', 'ASC')
        ->orderBy('paid_month', 'ASC')
        ->orderBy('paid_day', 'ASC')
        ->get(['paid_year AS year','paid_month AS month','paid_day AS day','amount']);

        $total_revenue['data'] = [];
        $total_revenue['amount'] = 0;

        foreach($revenues as $revenue){
            if($revenue->serial<=$range['latest']['serial'] && $revenue->serial>=$range['earliest']['serial']){
                $total_revenue['data'][] = $revenue;
                $total_revenue['amount'] += $revenue->amount;
            }
        }

        $expenses = Transaction::where('type', 'expense')
        ->whereNull('document_id')
        ->whereNull('contact_id')
        ->where('paid_year', '>=', $range['earliest']['year'])
        ->where('paid_year', '<=', $range['latest']['year'])
        ->orderBy('paid_year', 'ASC')
        ->orderBy('paid_month', 'ASC')
        ->orderBy('paid_day', 'ASC')
        ->get(['paid_year AS year','paid_month AS month','paid_day AS day','amount']);

        $total_expense['data'] = [];
        $total_expense['amount'] = 0;

        foreach($expenses as $expense){
            if($expense->serial<=$range['latest']['serial'] && $expense->serial>=$range['earliest']['serial']){
                $total_expense['data'][] = $expense;
                $total_expense['amount'] += $expense->amount;
            }
        }

        /*
        return json_encode([
            'range'=>$range,
            'bills'=>$total_bill,
            'invoices'=>$total_invoice,
            'payments'=>$total_payment,
            'revenues'=>$total_revenue,
            'expense'=>$total_expense,
        ]);
        
        foreach($total_revenue['data'] as $revenue){
            $var[$revenue->year][] = $revenue;    
        }

        return $var;

        foreach($var as $year=>$list){
            foreach($list as $data){
                $var2[$year][(int)$data->month][] = $data; 
            }  
        }

        return $var2;
        */

        //By Year

        // if($monthly || $yearly){
        //     $var = null;
        //     foreach($total_bill['data'] as $bill){
        //         $var[$bill->year][] = $bill;    
        //     }
        //     $total_bill['data'] = $var;
            
        //     $var = null;
        //     foreach($total_invoice['data'] as $invoice){
        //         $var[$invoice->year][] = $invoice;    
        //     }
        //     $total_invoice['data'] = $var;

        //     $var = null;
        //     foreach($total_payment['data'] as $payment){
        //         $var[$payment->year][] = $payment;    
        //     }
        //     $total_payment['data'] = $var;

        //     $var = null;
        //     foreach($total_revenue['data'] as $revenue){
        //         $var[$revenue->year][] = $revenue;    
        //     }
        //     $total_revenue['data'] = $var;

        //     $var = null;
        //     foreach($total_expense['data'] as $expense){
        //         $var[$expense->year][] = $expense;    
        //     }
        //     $total_expense['data'] = $var;
        // }

        // //By Month

        // if($monthly){
        //     $var = null;
        //     foreach($total_bill['data'] as $year=>$list){
        //         foreach($list as $data){
        //             $var[$year][(int)$data->month][] = $data; 
        //         }  
        //     }
        //     $total_bill['data'] = $var;
            
        //     $var = null;
        //     foreach($total_invoice['data'] as $invoice){
        //         foreach($list as $data){
        //             $var[$year][(int)$data->month][] = $data; 
        //         }   
        //     }
        //     $total_invoice['data'] = $var;

        //     $var = null;
        //     foreach($total_payment['data'] as $payment){
        //         foreach($list as $data){
        //             $var[$year][(int)$data->month][] = $data; 
        //         }  
        //     }
        //     $total_payment['data'] = $var;

        //     $var = null;
        //     foreach($total_revenue['data'] as $revenue){
        //         foreach($list as $data){
        //             $var[$year][(int)$data->month][] = $data; 
        //         }  
        //     }
        //     $total_revenue['data'] = $var;

        //     $var = null;
        //     foreach($total_expense['data'] as $expense){
        //         foreach($list as $data){
        //             $var[$year][(int)$data->month][] = $data; 
        //         }  
        //     }
        //     $total_expense['data'] = $var;
        // }


        // By Day


        // $var = [];
        // foreach($total_bill['data'] as $revenue){
        //     // $var[$revenue->serial][] = $revenue->amount;    
        //     if(array_key_exists($revenue->serial, $var)){
        //         $var[$revenue->serial] += $revenue->amount; 
        //     }else{
        //         $var[$revenue->serial] = $revenue->amount; 
        //     }
        // }
        $total_bill['data'] = $this->aggregateAmount($total_bill['data']);
        $total_revenue['data'] = $this->aggregateAmount($total_revenue['data']);
        $total_invoice['data'] = $this->aggregateAmount($total_invoice['data']);
        $total_payment['data'] = $this->aggregateAmount($total_payment['data']);
        $total_expense['data'] = $this->aggregateAmount($total_expense['data']);

        return json_encode([
            'range'=>$range,
            'bills'=>$total_bill,
            'invoices'=>$total_invoice,
            'payments'=>$total_payment,
            'revenues'=>$total_revenue,
            'expenses'=>$total_expense,
        ]);
    }

    /*Aggregrate Ammount by Day*/
    private function aggregateAmount($array){
        $var = [];
        foreach($array as $data){
            if(array_key_exists($data->serial, $var)){
                $var[$data->serial] += $data->amount; 
            }else{
                $var[$data->serial] = $data->amount; 
            }
            // $var[$revenue->serial][] = $revenue->amount; 
        }
        return $var;
    }
}