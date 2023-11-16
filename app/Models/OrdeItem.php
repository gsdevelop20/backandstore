<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrdeItem extends Model
{
    protected $table = 'OrderItems';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function setOrderItem($data)
    {
        $this->OrderID = $data['orderid'];
        $this->ProductID = $data['productid'];
        $this->Quantity = $data['quantity'];
        $this->OrderDate = time();
        $this->Subtotal = $data['subtotal'];

        if ($this->save()) {
            return true;
        }
        return false;
    }

    public function deleteOrderbyId($id){

        $order = DB::table($this->table)->where('OrderItemID', $id)->first();

        if ($order) {
            DB::table($this->table)->where('OrderItemID', $id)->delete();
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
