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

    public function get_report_sale(){
        $data = [];

        $sql = "SELECT
                    p.ProductID AS productid,
                    p.ProductName AS productname,
                    p.Category AS productcategory,
                    COALESCE(SUM(CASE WHEN o.Status != 'PAGAMENTO PENDENTE' THEN oi.Quantity ELSE 0 END), 0) AS quantity,
                    COALESCE(SUM(CASE WHEN o.Status != 'PAGAMENTO PENDENTE' THEN oi.Subtotal ELSE 0 END), 0) AS total
                FROM
                    Users u
                    JOIN Products p ON u.userId = p.SellerID
                    LEFT JOIN OrderItems oi ON oi.ProductID = p.ProductID
                    LEFT JOIN Orders o ON o.OrderID = oi.OrderID
                WHERE
                    u.userId = $this->userid
                GROUP BY
                    p.ProductID,
                    p.ProductName,
                    p.Category
                ORDER BY
                    p.ProductID";

        if($record = DB::select($sql)){
            $data = $record;
        }

        return $data;
    }

    public function get_report_order(){
        $data = [];

        $sql = "SELECT
                    oi.OrderItemID AS ordernumber,
                    p.ProductName AS productname,
                    p.Category AS productcategory,
                    oi.Quantity AS quantity,
                    oi.Subtotal AS subtotal,
                    o.Status AS paymentstatus,
                    DATE_FORMAT(FROM_UNIXTIME(oi.OrderDate), '%d/%m/%Y %H:%i') AS orderdate
                FROM
                    Users u
                    JOIN Products p ON u.userId = p.SellerID
                    JOIN OrderItems oi ON oi.ProductID = p.productID
                    JOIN Orders o on o.OrderID = oi.OrderItemID
                WHERE
                        u.userId = $this->userid
                GROUP BY
                    p.ProductName,
                    p.Category,
                    oi.OrderItemID,
                    p.ProductID;";

        if($record = DB::select($sql)){
            $data = $record;
        }

        return $data;
    }
}
