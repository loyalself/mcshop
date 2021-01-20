<?php
namespace App\Http\Services;
/**
 * 封装一个单例服务基类
 * 了解 self 与 static 的区别
 */
class BaseServices
{
    //private static $instance;
    //修改成 protected 是为了可以让子类可以使用
    protected static $instance;

    /**
     * 这里返回 static 在控制器层,idea才能智能感知 类名::getInstance()->调用当前类下的方法
     * @return static
     */
    public static function getInstance(){
        if(static::$instance instanceof static){
            return static::$instance;
        }
        static::$instance = new static();
        return static::$instance;
    }

    private function __construct(){}

    private function __clone(){}
}
