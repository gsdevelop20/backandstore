<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class sales_report
{
    public $userid;
    public function __construct(Request $request, $userid){
        $this->userid = $userid;
    }

    public function get_report(){
        $data = [];

        $sql = "SELECT
                    p.ProductID as productid,
                    p.ProductName AS productname,
                    p.Category AS productcategory,
                    CASE WHEN SUM(oi.Quantity) is null THEN 0 ELSE SUM(oi.Quantity) END AS quantity
                FROM
                    Users u
                    JOIN Products p ON u.userId = p.SellerID
                    LEFT JOIN OrderItems oi ON oi.ProductID = p.productID
                WHERE
                    u.userId = $this->userid
                GROUP BY
                    p.ProductName,
                    p.Category,
                    p.ProductID";

        if($record = DB::select($sql)){
            $data = $record;
        }

        return $data;
    }
}
