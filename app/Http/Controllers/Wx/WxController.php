<?php
namespace App\Http\Controllers\Wx;
use App\CodeReponse;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Traits\VerifyRequestInput;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WxController extends Controller
{
    use VerifyRequestInput;

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
    protected function codeReturn(array $codeResponse,$data = null,$info = ''){ //优化,添加 $info
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

    protected function successPaginate($page){
        return $this->success($this->paginate($page));
    }

    /**
     * 自定义分页器
     * @param LengthAwarePaginator|array $page
     * @return array
     */
    protected function paginate($page){
        if($page instanceof LengthAwarePaginator){
            return [
                'total' => $page->total(),         //总数
                'page'  => $page->currentPage(),   //当前页码
                'limit' => $page->perPage(),      //一页显示几条
                'pages' => $page->lastPage(),     //总共多少页
                'list'  => $page->items()         //数据源
            ];
        }else if($page instanceof Collection){
            $page = $page->toArray();
        }

        if(!is_array($page)) return $page;
        $total = count($page);
        return [
            'total' => $total,
            'page'  => 1,
            'limit' => $total,
            'pages' => 1,
            'list'  => $page
        ];
    }
    /**
     * 判断用户是否登录
     * @return bool
     */
    public function isLogin(){
        return !is_null($this->user());
    }
    /**
     * 获取用户id
     * @return mixed
     */
    public function userId(){
        return $this->user()->getAuthIdentifier(); //这个写法相当于  $this->user()->id;
    }

  /*  public function verifyId($key,$default = null){
        $value = $this->verifyData($key,$default,'integer|digits_between:1,20');
        return $value;
        $value = request()->input($key,$default);
        //make方法参数:第一个是要验证的数据,第二个是规则
        $validator = Validator::make(
            [
                $key => $value
            ],
            [
                $key => 'integer|digits_between:1,20'
            ]
        );
        if($validator->fails()) throw new BusinessException(CodeReponse::PARAM_ILLEGAL);
    }

    public function verifyString($key,$default = null){
        $value = $this->verifyData($key,$default,'string');
        $value = request()->input($key,$default);
        $validator = Validator::make(
            [
                $key => $value
            ],
            [
                $key => 'string'
            ]
        );
        if($validator->fails()) throw new BusinessException(CodeReponse::PARAM_ILLEGAL);
        return $value;
    }


    public function verifyBoolean($key,$default = null){
        return $this->verifyData($key,$default,'boolean');
    }

    public function verifyInteger($key,$default = null){
        return $this->verifyData($key,$default,'integer');
    }

    public function verifyEnum($key,$default = null,$enum = []){
        return $this->verifyData($key,$default,Rule::in($enum));
    }

    public function verifyData($key,$default,$rule){
        $value = request()->input($key,$default);
        $validator = Validator::make(
            [
                $key => $value
            ],
            [
                $key => $rule
            ]
        );
        if(is_null($default) && is_null($value)) return $value;
        if($validator->fails()) throw new BusinessException(CodeReponse::PARAM_ILLEGAL);
        return $value;
    }*/
}
