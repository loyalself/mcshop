<?php

use Illuminate\Support\Facades\Route;

Route::get('auth/register','AuthController@register'); //注册
Route::get('auth/login','AuthController@login');       //登录
Route::get('auth/user','AuthController@info');         //测试根据token获取用户信息
Route::get('auth/logout','AuthController@logout');     //退出登录
Route::get('auth/reset','AuthController@reset');      //密码重置
Route::get('auth/profile','AuthController@profile');  //用户信息修改

//用户模块--地址
Route::get('address/list','AddressController@list');        //收货地址列表
Route::get('address/detail','AddressController@detail');    //收货地址详情
Route::post('address/save','AddressController@save');        //保存收货地址
Route::post('address/delete','AddressController@delete');    //删除收货地址

//商品模块--类目
Route::get('catalog/index','CatalogController@index');       //分类列表全部分类数据
Route::get('catalog/current','CatalogController@current');   //分类目录当前分类数据

Route::get('brand/list','BrandController@list'); //品牌列表
Route::get('brand/detail','BrandController@detail'); //品牌详情

Route::get('goods/coount','GoodsController@count');       //统计商品总数
Route::get('goods/category','GoodsController@category');  //根据分类获取商品列表数据
Route::get('goods/list','GoodsController@list');          //获取商品列表
Route::get('goods/detail','GoodsController@detail');      //获取商品详情
