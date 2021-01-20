<?php
namespace App;
/**
 * 定义统一的返回码
 */
class CodeReponse
{
    //通用返回码
    const SUCCESS = [0, '成功'];
    const FAIL    = [-1, '错误'];
    const PARAM_ILLEGAL = [401,'参数不合法'];
    //业务返回码
    const AUTH_NAME_REGISTERED   =  [704,'用户名已注册'];
    const AUTH_MOBILE_REGISTERED =  [705,'手机号已注册'];
    const AUTH_INVALID_MOBILE    =  [707,'手机号格式不正确'];
    const AUTH_CAPTCHA_FREQUENCY = [702,'验证码还未过期'];
    const AUTH_CAPTCHA_UNMATCH   = [703,'验证码错误'];
}
