<?php

namespace App\Models;

use http\Env\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserInfor extends Model
{


    protected $table = 'UserInfo';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function setUserInfo($userId, $address){

        $this->userId = $userId;
        $this->address = $address;

        $this->save();
    }

    public static function get_user_info_by_user_id($userid){
        return DB::table('UserInfo')->where('userId', $userid)->first();
    }

}
