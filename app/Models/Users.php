<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'Users';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function setUser($data)
    {

        $this->firstName = $data['firstname'];
        $this->lastName = $data['lastName'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->registrationDate = strval(time());

        if ($this->save()) {
            $userInfo = new UserInfor();
            $userInfo->setUserInfo($this->id, $data['address'] ?? '');
        } else {
            return false;
        }
        return true;
    }
}
