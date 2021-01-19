<?php


namespace App\Http\Controllers\Wx;


use App\CodeReponse;
use App\Http\Controllers\Controller;

class WxController extends Controller
{
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

  /*  protected function fail(array $codeResponse){
        return $this->codeReturn($codeResponse);
    }*/

    protected function fail(array $codeResponse = CodeReponse::FAIL,$info = ''){
        return $this->codeReturn($codeResponse,null,$info);
    }
}
