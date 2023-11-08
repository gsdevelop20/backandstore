<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Reviews extends Model
{
    protected $table = 'Reviews';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function setComent($data)
    {

        $this->UserID = $data['userid'];
        $this->ProductID = $data['productid'];
        $this->Comment = $data['comment'];
        $this->ReviewDate = time();

        if ($this->save()) {
            return true;
        }

        return false;
    }

    public function deleteReview($Reviewid)
    {
        $Review = DB::table('Reviews')->where('ReviewID', $Reviewid)->first();

        if ($Review) {
            DB::table('Reviews')->where('ReviewID', $Reviewid)->delete();
            return true;
        } else {
            return false;
        }
    }

    public function editReview($id, $comment)
    {
        $Review = DB::table('Reviews')->where('ReviewID', $id)->first();

        if ($Review) {
            DB::table('Reviews')->where('ReviewID', $id)->update(['Comment' => $comment]);
            return true;
        } else {
            return false;
        }
    }

    public static function get_reviews_by_productid($productid)
    {
        return DB::table('Reviews')->where('ProductID', $productid)->orderBy('ReviewDate', 'desc')->get();
    }
}

