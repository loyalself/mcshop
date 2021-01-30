<?php
namespace App\Http\Traits;
use App\CodeReponse;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
/**
 * 将验证逻辑写到 Wxcontroller里不好,所以新建一个验证代码块,即 trait
 * Trait VerifyRequestInput
 * @package App\Http\Traits
 */
trait VerifyRequestInput
{
    public function verifyId($key,$default = null){
        return $this->verifyData($key,$default,'integer|digits_between:1,20');
    }

    public function verifyString($key,$default = null){
        return $this->verifyData($key,$default,'string');
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
    }
}
