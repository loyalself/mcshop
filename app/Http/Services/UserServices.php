<?php
namespace App\Http\Services;
use App\CodeReponse;
use App\Exceptions\BusinessException;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserServices extends BaseServices
{
   /* private static $instance;

    public static function getInstance(){
        if(self::$instance instanceof self){
          return self::$instance;
        }
        self::$instance = new self();
        return self::$instance;
    }

    private function __construct(){}

    private function __clone(){}*/

    /**
     * 根据用户名获取当前用户
     * @param $username
     * @return \Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByUsername($username){
        return User::query()
            ->where('username',$username)
            ->where('deleted',0)
            ->first();
    }
    /**
     * 根据手机号获取当前用户
     * @param $mobile
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByMobile($mobile){
        return User::query()
            ->where('mobile',$mobile)
            ->where('deleted',0)
            ->first();
    }
    /**
     * 检查验证码是否正确
     * @param string $mobile
     * @param string $code
     * @return bool
     * @throws BusinessException
     */
    public function checkCaptcha(string $mobile,string $code){
        //如果不是生产环境,直接跳过验证码验证
        if(!app()->environment('production')){
            return true;
        }
        $key = 'register_captcha_'.$mobile;
        $isPass = $code === Cache::get($key);
        if($isPass){
            Cache::forget($key);
            return true;
        }else{
            throw new BusinessException(CodeReponse::AUTH_CAPTCHA_UNMATCH);
        }
    }
}
