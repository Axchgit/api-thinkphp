<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 11:15:58
 * @LastEditTime: 2020-08-20 16:39:01
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\route\app.php
 */
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('think', function () {
    return 'hello,ThinkPHP6!';
});

Route::get('hello/:name', 'index/hello');

/****测试*****/
Route::get('test/testthree','Test/testThree')->middleware('checkrequest',6);
Route::get('test/testfive','Test/testFive')->middleware('checkrequest',6);


/****登录模块*****/
// Route::resource('login', 'Login');
// Route::rest('save', ['POST', '/sendCode', 'sendcode']);
//发送验证码请求
Route::rule('login/sendadmincode','Login/sendCode');
//验证登录请求
Route::rule('login/checkadminlogin','Login/checkAdminLogin');
//获取管理员信息请求
Route::rule('login/selectadmininfo','Login/selectInfo')->middleware('checkrequest',1);
//员工登录
Route::rule('login/checkEmplogin','Login/checkEmpLogin');
Route::rule('login/selectempinfo','Login/selectEmpInfo')->middleware('checkrequest',5);


/****获取员工信息*****/
// Route::resource('employee','Employee');
Route::rule('employee/selectall', 'Employee/selectAll')->middleware('checkrequest',1);
//根据工号获取信息,工号为8位数字
Route::rule('employee/selectByInfo', 'Employee/selectByInfo')->middleware('checkrequest',1);// Route::get('employee/selectbyname', 'Employee/selectByName')->middleware('checkrequest',1);
//根据权限获取信息
Route::rule('employee/selectByRule','Employee/selectByRule')->middleware('checkrequest',1);
