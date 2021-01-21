<?php

use Illuminate\Support\Facades\Route;

Route::get('auth/register','AuthController@register'); //注册
Route::get('auth/login','AuthController@login');    //登录
