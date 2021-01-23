<?php

use Illuminate\Support\Facades\Route;

Route::get('auth/register','AuthController@register'); //注册
Route::get('auth/login','AuthController@login');       //登录
Route::get('auth/user','AuthController@info');         //测试根据token获取用户信息
Route::get('auth/logout','AuthController@logout');     //退出登录
Route::get('auth/reset','AuthController@reset');      //密码重置
Route::get('auth/profile','AuthController@profile');  //用户信息修改
