<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class sales_report extends Controller
{
    public function report_sale(Request $request, $userid){
        $sales_report = new \App\Services\sales_report($request, $userid);
        return $sales_report->get_report_sale();
    }

    public function report_order(Request $request, $userid){
        $sales_report = new \App\Services\sales_report($request, $userid);
        return $sales_report->get_report_order();
    }
}
