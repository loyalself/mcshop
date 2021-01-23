<?php
namespace App\Http\Controllers\Wx;
use App\CodeReponse;
use App\Exceptions\BusinessException;
use App\Http\Services\UserServices;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
class AuthController extends WxController
{
   /* public function __construct(){
        $this->middleware('auth:wx',[
            'only' => ['info'],  //白名单,这里面的方法不用验证
            //'except' => [] //黑名单
        ]);
    }*/

    protected $only = ['info'];

    /**
     * 获取用户基础信息
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(){
        //$user = Auth::guard('wx')->user();
        $user = $this->user();
        return $this->success([
            'nickname' => $user->nickname,
            'avatar'   => $user->avatar,
            'gender'   => $user->gender,
            'mobile'   => $user->mobile
        ]);
    }

    /**
     * 用户信息修改
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request){
        $user = $this->user();
        $avatar = $user->avatar;
        $gender = $user->gender;
        $nickname = $user->nickname;

        if(!empty($avatar)){
            $user->avatar = $avatar;
        }

        if(!empty($avatar)){
            $user->gender = $gender;
        }

        if(!empty($nickname)){
            $user->nickname = $nickname;
        }
        $res = $user->save();
        return $this->failOrSuccess($res,CodeReponse::UPDATED_FAIL);
    }

    /**
     * 用户重置密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BusinessException
     */
    public function reset(Request $request){
        $code = $request->input('code');
        $password = $request->input('password');
        $mobile = $request->input('mobile');
        if(empty($code) || empty($password || empty($mobile))){
            return $this->fail(CodeReponse::PARAM_ILLEGAL);
        }
        $isPass = UserServices::getInstance()->checkCaptcha($mobile,$code);
        if(!$isPass) return $this->fail(CodeReponse::AUTH_CAPTCHA_UNMATCH);

        $user = UserServices::getInstance()->getByMobile($mobile);
        if(is_null($user)) return $this->fail(CodeReponse::AUTH_MOBILE_UNREGISTERED);

        $user->password = Hash::make($password);
        $res = $user->save();
        //return $res ? $this->success() : $this->fail(CodeReponse::UPDATED_FAIL);
        //优化 return
        return $this->failOrSuccess($res,CodeReponse::UPDATED_FAIL);
    }

    /**
     * 用户退出登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
        Auth::guard('wx')->logout();
        return $this->success();
    }
    /**
     * 用户登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        $username = $request->input('username');
        $password = $request->input('password');
        if(empty($username) || empty($password)) return $this->fail(CodeReponse::PARAM_ILLEGAL);

        $user = UserServices::getInstance()->getByUsername($username);
        if(is_null($user)) return $this->fail(CodeReponse::AUTH_INVALID_ACCOUNT);

        //getAuthPassword()是 Authenticatable 里的方法
        $isPass = Hash::check($password,$user->getAuthPassword());
        if(!$isPass) return $this->fail(CodeReponse::AUTH_INVALID_ACCOUNT,'账号密码不对');
        //更新登录信息
        $user->last_login_time = now()->toDateTimeString();
        $user->last_login_ip = $request->getClientIp();
        if(!$user->save()) return $this->fail(CodeReponse::UPDATED_FAIL);
        //从jwt中获取token
        $token = Auth::guard('wx')->login($user);
        //返回登录数据
        return $this->success([
            'token'    => $token,
            'userInfo' => [
                'nickName'  => $username,
                'avatarUrl' => $user->avatar
            ]
        ]);
    }
    /**
     * 注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request){
        //throw new BusinessException([999,'hehehe'],'aaaaa');
        $username = $request->input('username');
        $password = $request->input('password');
        $mobile   = $request->input('mobile');
        $code     = $request->input('code');

        if(empty($username) || empty($password) || empty($mobile) || empty($code)){
            //return ['errno'=>401,'errmsg'=>'参数不对'];
            return $this->fail(CodeReponse::PARAM_ILLEGAL);
        }
        //$user = (new UserServices())->getByUsername($username);
        $user = UserServices::getInstance()->getByUsername($username);
        if(!is_null($user)){
            //return ['errno'=>704,'errmsg'=>'用户名已注册'];
            return $this->fail(CodeReponse::AUTH_NAME_REGISTERED);
        }
        //$user = (new UserServices())->getByMobile($mobile);
        $user = UserServices::getInstance()->getByMobile($mobile);
        if(!is_null($user)){
            //return ['errno'=>705,'errmsg'=>'手机号已注册'];
            return $this->fail(CodeReponse::AUTH_MOBILE_REGISTERED);
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

        /*return [
            'errno'  => 0,
            'errmsg' => '成功',
            'data'   => [
                'token'    => '',
                'userInfo' => [
                    'nickName'  => $username,
                    'avatarUrl' => $user->avatar
                ]
            ]
        ];*/

        return $this->success([
            'token'    => '',
            'userInfo' => [
                'nickName'  => $username,
                'avatarUrl' => $user->avatar
            ]
        ]);
    }

    /**
     * 注册发送验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function regCaptach(Request $request){
        $mobile = $request->input('mobile');
        if(empty($mobile)) {
            return $this->fail(CodeReponse::PARAM_ILLEGAL);
        }

        //$user = (new UserServices())->getByMobile($mobile);
        $user = UserServices::getInstance()->getByMobile($mobile);
        if(!is_null($user)) {
            return $this->fail(CodeReponse::AUTH_MOBILE_REGISTERED);
        }
        //生成随机验证码
        $code = random_int(100000,999999);
        //防刷验证码生成验证,一分钟内只能请求一次,一天10次;一分钟内已经添加过一次再添加就返回 false,即添加失败
        $lock = Cache::add('register_captach_lock_'.$mobile,1,60);
        if(!$lock) {
            //702:相同的返回码,但是返回信息不同,改造返回方法
            //return ['errno'=>702,'errmsg'=>'验证码未过期,不能再发送'];
            return $this->fail(CodeReponse::AUTH_CAPTCHA_FREQUENCY);
        }

        $countKey = 'register_captach_count_'.$mobile;
        if(Cache::has($countKey)){
            $count = Cache::increment($countKey);
            if($count > 10){
                //return ['errno'=>702,'errmsg'=>'验证码当天发送不能超过10次'];
                return $this->fail(CodeReponse::AUTH_CAPTCHA_FREQUENCY,'验证码当天发送不能超过10次');
            }
        }else{
            Cache::put('register_captach_'.$mobile,$code,600);
        }
        return $this->success();
        //todo 发送短信
    }
}
