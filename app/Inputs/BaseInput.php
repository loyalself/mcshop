<?php
namespace App\Inputs;
use App\CodeReponse;
use App\Exceptions\BusinessException;
use App\Http\Traits\VerifyRequestInput;
use Illuminate\Support\Facades\Validator;

class BaseInput
{
    use VerifyRequestInput;

  /*  public function fill(){
        return $this;
    }*/

    public function fill($data = null){
        if(is_null($data)){
            $data = request()->input();
        }
        $validator = Validator::make($data,$this->rules());
        if($validator->fails()){
            throw new BusinessException(CodeReponse::PARAM_ILLEGAL);
        }
        $map = get_object_vars($this); //获取当前对象定义了哪些属性
        $keys = array_keys($map);
        collect($data)->map(function ($v,$k) use ($keys){
            if(in_array($k,$keys)){
                $this->$k = $v;
            }
        });
        return $this;
    }

    public function rules(){
        return [];
    }

    /**
     * @return BaseInput | static (当前 new 的类是子类就是子类,父类就是父类)
     */
    //public static function new(){
    public static function new($data = null){
        return (new static())->fill($data);
    }

}
