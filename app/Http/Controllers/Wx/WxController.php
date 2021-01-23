<?php


namespace App\Http\Controllers\Wx;


use App\CodeReponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class WxController extends Controller
{
    protected $only;

    protected $except;

    /**
     * 添加中间件限制以及白名单和黑名单设置
     */
    public function __construct(){
       $option = [];
       if(!is_null($this->only)){
           $option['only'] = $this->only;
       }
       if(!is_null($this->except)){
           $option['except'] = $this->except;
       }
       $this->middleware('auth:wx',$option);
    }


    //protected function codeReturn($errno,$errmsg,$data = null){
    protected function codeReturn(array $codeResponse,$data = null,$info = ''){ //优化
       /* $res = [
            'errno' =>$errno,
            'errmsg'=>$errmsg
        ];*/
        list($errno,$errmsg) = $codeResponse;
        $res = [
            'errno' => $errno,
            'errmsg'=> $info ?: $errmsg
        ];
        if(!is_null($data)){
            $res['data'] = $data;
        }
        return response()->json($res);
    }

    protected function success($data = null){
        //return $this->codeReturn(0,'成功',$data);
        return $this->codeReturn(CodeReponse::SUCCESS,$data);
    }

    /*protected function fail($errno,$errmsg){
        return $this->codeReturn($errno,$errmsg);
    }*/

    /*protected function fail(array $codeResponse){
        return $this->codeReturn($codeResponse);
    }*/

    protected function fail(array $codeResponse = CodeReponse::FAIL,$info = ''){
        return $this->codeReturn($codeResponse,null,$info);
    }

    protected function failOrSuccess($isSuccess,array $codeResponse = CodeReponse::FAIL,$data = null,$info = ''){
        if($isSuccess){
            return $this->success($data);
        }else{
            return $this->fail($codeResponse,$info);
        }
    }

    /**
     * @return User|null
     */
    public function user(){
        return Auth::guard('wx')->user();
    }
}
