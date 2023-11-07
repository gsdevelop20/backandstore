<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Products extends Model
{
    protected $table = 'Products';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function setProduct($data)
    {

        $this->SellerID = $data['sellerid'];
        $this->ProductName = $data['productname'];
        $this->Description = $data['description'];
        $this->Price = $data['price'];
        $this->StockQuantity = $data['stockquantity'];
        $this->Category = $data['category'];

        $img = $data['productimg'];
        if (!empty($img)) {
            $extension = $img->extension();
            $imgName = md5($img->getClientOriginalName()) . '.' . $extension;
            $img->move(storage_path('app/public/productImg'), $imgName);

            $this->url = $imgName;
        }

        if ($this->save()) {
            return true;
        }
        return false;
    }

    public static function get_product_by_id($productid)
    {

        return DB::table('Products')->where('ProductID', $productid)->first();
    }
}
