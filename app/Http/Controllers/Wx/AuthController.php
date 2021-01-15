<?php

namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Controller;
use App\Http\Services\UserServices;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile   = $request->input('mobile');
        $code     = $request->input('code');

        if(empty($username) || empty($password) || empty($password) || empty($code)){
            return ['errno'=>401,'errmsg'=>'参数不对'];
        }
        $user = (new UserServices())->getByUsername($username);
        if(!is_null($user)){
            return ['errno'=>704,'errmsg'=>'用户名已注册'];
        }
        $user = (new UserServices())->getByMobile($mobile);
        if(!is_null($user)){
            return ['errno'=>705,'errmsg'=>'手机号已注册'];
        }

        $user                  = new User();
        $user->username        = $username;
        $user->password        = Hash::make($password);
        $user->mobile          = $mobile;
        $user->avatar          = 'http://xxxx';
        $user->nickname        = $username;
        $user->last_login_time = Carbon::now()->toDateTimeString();
        $user->last_login_ip   = $request->getClientIp();
        $user->save();

        return [
            'errno'  => 0,
            'errmsg' => '成功',
            'data'   => [
                'token'    => '',
                'userInfo' => [
                    'nickName'  => $username,
                    'avatarUrl' => $user->avatar
                ]
            ]
        ];

    }
}
