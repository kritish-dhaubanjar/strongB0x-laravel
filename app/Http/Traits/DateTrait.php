<?php
namespace App\Http\Traits;

use App\Models\Transaction;
use App\Models\Purchases\Bill;
use App\Models\Sales\Invoice;

trait DateTrait {

    private $BS = [
        2070=>[31,31,31,32,31,31,29,30,30,29,30,30],
        2071=>[31,31,32,31,31,31,30,29,30,29,30,30],
        2072=>[31,32,31,32,31,30,30,29,30,29,30,30],
        2073=>[31,32,31,32,31,30,30,30,29,29,30,31],
        2074=>[31,31,31,32,31,31,30,29,30,29,30,30],
        2075=>[31,31,32,31,31,31,30,29,30,29,30,30],
        2076=>[31,32,31,32,31,30,30,30,29,29,30,30],
        2077=>[31,32,31,32,31,30,30,30,29,30,29,31],
        2078=>[31,31,31,32,31,31,30,29,30,29,30,30],
        2079=>[31,31,32,31,31,31,30,29,30,29,30,30],
        2080=>[31,32,31,32,31,30,30,30,29,29,30,30],
        2081=>[31,31,32,32,31,30,30,30,29,30,30,30],
        2082=>[30,32,31,32,31,30,30,30,29,30,30,30],
        2083=>[31,31,32,31,31,30,30,30,29,30,30,30],
        2084=>[31,31,32,31,31,30,30,30,29,30,30,30],
        2085=>[31,32,31,32,30,31,30,30,29,30,30,30],
        2086=>[30,32,31,32,31,30,30,30,29,30,30,30],
        2087=>[31,31,32,31,31,31,30,30,29,30,30,30],
        2088=>[30,31,32,32,30,31,30,30,29,30,30,30],
        2089=>[30,32,31,32,31,30,30,30,29,30,30,30],
        2090=>[30,32,31,32,31,30,30,30,29,30,30,30]
    ];

    public function getActiveDate(){
        $latest_bill_date = Bill::orderBy('billed_year', 'DESC')->orderBy('billed_month', 'DESC')->orderBy('billed_day', 'DESC')->get(['billed_year AS year','billed_month AS month','billed_day AS day'])->first();
        $latest_invoice_date = Invoice::orderBy('invoiced_year', 'DESC')->orderBy('invoiced_month', 'DESC')->orderBy('invoiced_day', 'DESC')->get(['invoiced_year AS year','invoiced_month AS month','invoiced_day AS day'])->first();
        $latest_transaction_date = Transaction::orderBy('paid_year', 'DESC')->orderBy('paid_month', 'DESC')->orderBy('paid_day', 'DESC')->get(['paid_year AS year','paid_month AS month','paid_day AS day'])->first();

        $latest_bill_date['serial'] = $latest_bill_date['year'] . $latest_bill_date['month'] .$latest_bill_date['day'];
        $latest_invoice_date['serial'] = $latest_invoice_date['year'] . $latest_invoice_date['month'] .$latest_invoice_date['day'];
        $latest_transaction_date['serial'] = $latest_transaction_date['year'] . $latest_transaction_date['month'] .$latest_transaction_date['day'];

        $latest_active_date = $latest_bill_date;

        if($latest_active_date['serial'] < $latest_invoice_date['serial']){
            $latest_active_date = $latest_invoice_date;
        }
        
        if($latest_active_date['serial'] < $latest_transaction_date['serial']){
            $latest_active_date = $latest_transaction_date ;
        }

        return $latest_active_date;
    }

    public function getEarliestDate(){
        $latest_bill_date = Bill::orderBy('billed_year', 'ASC')->orderBy('billed_month', 'ASC')->orderBy('billed_day', 'ASC')->get(['billed_year AS year','billed_month AS month','billed_day AS day'])->first();
        $latest_invoice_date = Invoice::orderBy('invoiced_year', 'ASC')->orderBy('invoiced_month', 'ASC')->orderBy('invoiced_day', 'ASC')->get(['invoiced_year AS year','invoiced_month AS month','invoiced_day AS day'])->first();
        $latest_transaction_date = Transaction::orderBy('paid_year', 'ASC')->orderBy('paid_month', 'ASC')->orderBy('paid_day', 'ASC')->get(['paid_year AS year','paid_month AS month','paid_day AS day'])->first();

        $latest_bill_date['serial'] = $latest_bill_date['year'] . $latest_bill_date['month'] .$latest_bill_date['day'];
        $latest_invoice_date['serial'] = $latest_invoice_date['year'] . $latest_invoice_date['month'] .$latest_invoice_date['day'];
        $latest_transaction_date['serial'] = $latest_transaction_date['year'] . $latest_transaction_date['month'] .$latest_transaction_date['day'];

        $latest_active_date = $latest_bill_date;

        if($latest_active_date['serial'] > $latest_invoice_date['serial']){
            $latest_active_date = $latest_invoice_date;
        }
        
        if($latest_active_date['serial'] > $latest_transaction_date['serial']){
            $latest_active_date = $latest_transaction_date ;
        }

        return $latest_active_date;
    }

    public function getLifetime(){
        return [
            'latest'=>$this->getActiveDate(),
            'earliest'=>$this->getEarliestDate()
        ];
    }

    public function getBeforeDate($range){

        $latest = $this->getActiveDate();  //{"year": 2077, "month": "12", "day": "24", "serial": "20771224"}
        $earliest = clone $latest;        
        $difference = $latest->day - $range;

        if($difference > 0){
            $earliest->day = $difference > 9 ? "$difference" : "0$difference";
            /*
            }else if($difference == 0){
                if($latest->month == 1){
                    $earliest->month = "12";
                    $earliest->year = ($latest->year - 1);
                }else{
                    $earliest->month = ($latest->month - 1)."";
                }
                $earliest->day = $this->BS[$earliest->year][$earliest->month - 1]."";
            }else if($difference < 0){
            */
        }else{
            while($difference <= 0){
                if($earliest->month > 1){
                    $earliest->month -=1;
                }else{
                    $earliest->month = 12;
                    $earliest->year -=1;
                }
                $difference = $difference + $this->BS[$earliest->year][$earliest->month - 1];
                if($difference == 0){
                    if($earliest->month == 1){
                        $earliest->month = 12;
                        $earliest->year -=1;
                    }
                    $earliest->month -= 1;
                    $difference = $this->BS[$earliest->year][$earliest->month - 1];
                }
                $earliest->day = $difference;
            }
            $earliest->month = $earliest->month > 9 ? "$earliest->month" : "0$earliest->month";
            $earliest->day = $earliest->day > 9 ? "$earliest->day" : "0$earliest->day";
        }

        $earliest['serial'] = $earliest['year'] . $earliest['month'] .$earliest['day'];

        return [
            'latest'=>$latest,
            'earliest'=>$earliest,
        ];
    }

    public function getMonthDate($year, $month_index){
        $month = $month_index + 1;
        $month = $month > 9 ? $month."" : "0$month";

        $latest = [
            'year'=>$year,
            'month'=>$month,
            'day'=>$this->BS[$year][$month_index].""
        ];   ////["year"=> 2077, "month"=> "12", "day"=> "24", "serial"=> "20771224"]

        $earliest = $latest;
        $earliest['month'] = $month."";
        $earliest['day'] = "01";
        
        $earliest['serial'] = $earliest['year'] . $earliest['month'] .$earliest['day'];
        $latest['serial'] = $latest['year'] . $latest['month'] .$latest['day'];
        
        return [
            'latest'=>$latest,
            'earliest'=>$earliest
        ];
    }

}