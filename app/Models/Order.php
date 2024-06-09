<?php

namespace App\Models;
require_once '../vendor/autoload.php';

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $table = 'Orders';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function setOrder($data)
    {
        $this->UserID = $data['userid'];
        $this->Status = 'PAGAMENTO PENDENTE';

        $result = false;

        if ($this->save()) {
            $data['orderid'] = $this->id;
            $userInfo = new OrdeItem();
            $userInfo->setOrderItem($data);
            \App\Models\Products::update_stock_by_id($data['productid'], $data['stock'],$data['quantity'] );
        } else {
            return false;
        }
        return true;
    }

    public static function pix_qrcode_generete($email, $subtotal)
    {
        MercadoPagoConfig::setAccessToken("TEST-7060678731638258-030520-270223fbb6042104a0e5ef26cc523be4-284339554");
        $client = new PaymentClient();
        $data = [];
        try {
            $request = [
                "transaction_amount" => floatval($subtotal),
                "description" => "Pagameto",
                "payment_method_id" => "pix",
                "payer" => [
                    "email" =>  $email,
                ]
            ];

            // Step 5: Make the request
            $payment = $client->create($request);
            $data['qrcode_img'] = 'data:image/png;base64,' . $payment->point_of_interaction->transaction_data->qr_code_base64;
            $data['qrcode_link'] = $payment->point_of_interaction->transaction_data->qr_code;
            $data['paymentid'] = $payment->id;

        } catch (MPApiException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
        return $data;
    }

    public static function get_order_by_userid($userid)
    {
        return  DB::table('Orders')
            ->join('OrderItems', 'Orders.OrderID', '=', 'OrderItems.OrderID')
            ->where('Orders.UserID', '=', $userid)
            ->get();
    }

    public function delete_order_by_id($orderid, $ordemitemid){
        $order = DB::table($this->table)->where('OrderID', $orderid)->first();

        if ($order) {
            $order = new \App\Models\OrdeItem();
            $order->deleteOrderbyId($ordemitemid);
            DB::table($this->table)->where('OrderID', $orderid)->delete();
            return true;
        } else {
            return false;
        }
    }

    public static function get_user_by_id($user)
    {
        return DB::table('Users')->where('userId', $user)->first();
    }
}
