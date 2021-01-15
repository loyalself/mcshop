<?php
namespace App\Http\Services;
use App\Models\User;

class UserServices
{
    /**
     * 根据用户名获取当前用户
     * @param $username
     * @return User|null|Model
     */
    public function getByUsername($username){
        return User::query()
            ->where('username',$username)
            ->where('deleted',0)
            ->first();
    }

    public function getByMobile($mobile){
        return User::query()
            ->where('mobile',$mobile)
            ->where('deleted',0)
            ->first();
    }

}
