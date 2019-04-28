<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//    珠宝
Route::prefix('index')->group(function () {
    Route::get('index', 'IndexController@index');//首页
    Route::get('reg', 'IndexController@reg');//注册
    Route::get('login', 'IndexController@login');//登录

    Route::get('shop', 'IndexController@shop');//排序
    Route::get('prolist', 'IndexController@prolist');//列表展示

    Route::get('car', 'IndexController@car');//购物车
    Route::get('cartDel', 'IndexController@cartDel');//购物车删除单条
    Route::get('proinfo', 'IndexController@proinfo');//单条数据显示
    Route::get('newprice', 'IndexController@newprice');//购买件数得到总价
    Route::get('shoucang', 'IndexController@shoucang');//收藏
    Route::get('add', 'IndexController@add');//加入购物车
    Route::get('changeBuyNmber', 'IndexController@changeBuyNmber');//加入购物车
    Route::get('getSubTotal', 'IndexController@getSubTotal');//获取小计
    Route::get('pay/{cart_id}', 'IndexController@pay');//支付页面
    Route::post('success', 'IndexController@success');//成功页面
});
//我的
Route::prefix('user')->group(function () {
    Route::get('user', 'UserController@user');//我的
    Route::get('order', 'UserController@order');//待支付 代发货 待收货 全部订单
    Route::get('Aaaddress', 'UserController@Aaaddress');//收货地址管理
    Route::get('address', 'UserController@address');//详细地址
    Route::get('getcity', 'UserController@getcity');//获取市县
    Route::get('addressdo', 'UserController@addressdo');//添加地址处理页面
    Route::get('shoucang', 'UserController@shoucang');//收藏
    Route::get('delshou', 'UserController@delshou');//取消收藏
    Route::get('liu', 'UserController@liu');//浏览记录
    Route::get('delliu', 'UserController@delliu');//删除浏览记录
    Route::get('quanliu', 'UserController@quanliu');//全部删除浏览记录
    Route::get('tui', 'UserController@tui');//全部删除浏览记录
});

//    登录
Route::prefix('login')->group(function () {
    Route::get('reg', 'LoginController@reg');//注册
    Route::post('regdo', 'LoginController@regdo');//注册
    Route::get('login', 'LoginController@login');//登录
    Route::post('logindo', 'LoginController@logindo');//登录
    Route::post('checkEmail', 'LoginController@checkEmail');//
    Route::get('test', 'LoginController@test');//
});

//存储缓存
Route::get('log/register',"LogController@register");//注册
Route::post('log/registerdo',"LogController@registerdo");//注册执行
Route::post('log/sendEmail',"LogController@sendEmail");//发送验证码
Route::any('log/log',"LogController@log");//登录
Route::any('log/logdo',"LogController@logdo");//登录执行
Route::any('log/loglist',"LogController@loglist");//用户名列表
Route::any('log/test',"LogController@test");//

