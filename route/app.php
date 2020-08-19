<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 11:15:58
 * @LastEditTime: 2020-08-19 17:28:15
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

/****登录模块*****/
Route::resource('login', 'Login');
Route::rest('save', ['POST', '/sendCode', 'sendcode']);

/****获取员工信息*****/
// Route::resource('employee','Employee');
Route::get('employee/selectall', 'Employee/selectAll')->middleware('checkrequest',1);
//根据工号获取信息,工号为8位数字
Route::get('employee/selectByInfo', 'Employee/selectByInfo')->middleware('checkrequest',1);
// //根据姓名获取信息
// Route::get('employee/selectbyname', 'Employee/selectByName')->middleware('checkrequest',1);
//根据权限获取信息
Route::post('employee/selectByRule','Employee/selectByRule')->middleware('checkrequest',1);
