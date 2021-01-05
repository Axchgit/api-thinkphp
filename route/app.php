<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 11:15:58
 * @LastEditTime: 2021-01-04 19:33:42
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \testd:\wamp64\www\api-thinkphp\route\app.php
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
Route::get('test/testthree', 'Test/testThree')->middleware('checkrequest', 6)->allowCrossDomain();
Route::get('test/testone', 'Test/testOne')->middleware('checkrequest', 6)->allowCrossDomain();

Route::get('test/testfive', 'Test/testFive')->middleware('checkrequest', 6)->allowCrossDomain();

/****登录模块*****/
Route::group('login', function () {
    Route::rule('getAuthInfo/<qruid>/<userUuid?>/<isScan?>','getAuthInfo');
    Route::rule('phoneConfirmLogin/<qruid>','phoneConfirmLogin');

    
    //发送验证码请求
    Route::get('/', 'sendAdminCode')->allowCrossDomain();
    //验证登录请求
    Route::rule('checkadminlogin', 'checkAdminLogin')->allowCrossDomain();
    //获取管理员信息请求
    Route::rule('selectadmininfo', 'selectAdminInfo')->middleware('checkrequest', 1)->allowCrossDomain();
    //员工登录
    Route::rule('checkEmplogin', 'checkEmpLogin')->allowCrossDomain();
    Route::rule('selectempinfo', 'selectEmpInfo')->middleware('checkrequest', 5)->allowCrossDomain();
})->completeMatch()->prefix('Login/');
/********管理员 */
Route::group('admin', function () {
    //员工信息
    Route::post('uploadExcel', 'uploadExcel')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::get('selectEmployee', 'selectEmployee')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::post('updateEmployeeInfo', 'updateEmployeeInfo')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::post('deleteEmployeeInfo', 'deleteEmployeeInfo')->middleware('checkrequest', 1)->allowCrossDomain();
    //员工账户
    Route::post('selectEmployeeAccount', 'selectEmployeeAccount')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::post('updateEmployeeAccount', 'updateEmployeeAccount')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::post('deleteEmployeeAccount', 'deleteEmployeeAccount')->middleware('checkrequest', 1)->allowCrossDomain();
    //业绩
    Route::get('selectPerformance', 'selectPerformance')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::post('reviewPerformance', 'reviewPerformance')->middleware('checkrequest', 1)->allowCrossDomain();
    //动态
    Route::get('selectEmployeeLeave', 'selectEmployeeLeave')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::post('reviewEmployeeLeave', 'reviewEmployeeLeave')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::get('selectEmployeeQuit', 'selectEmployeeQuit')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::post('reviewEmployeeQuit', 'reviewEmployeeQuit')->middleware('checkrequest', 1)->allowCrossDomain();
    //反馈
    Route::get('selectFeedback', 'selectFeedback')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::post('reviewFeedback', 'reviewFeedback')->middleware('checkrequest', 1)->allowCrossDomain();


    


    // Route::post('selectGoods', 'selectGoods')->middleware('checkrequest', 1)->allowCrossDomain();
})->completeMatch()->prefix('Admin/');

/****员工*****/
// Route::resource('employee','Employee');
Route::group('employee', function () {
    // TODO:删除
    /************* */
    //获取员工所有信息
    Route::get('selectall', 'selectAll')->middleware('checkrequest', 1)->allowCrossDomain();
    //根据工号/姓名获取信息,工号为8位数字
    Route::get('selectByInfo', 'selectByInfo')->middleware('checkrequest', 1)->allowCrossDomain();
    //根据权限获取信息
    Route::get('selectByRole', 'selectByRole')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::get('selectAcAll', 'selectAcAll')->middleware('checkrequest', 1)->allowCrossDomain();
    /************** */

    Route::post('sendRecoverCode', 'sendRecoverCode')->allowCrossDomain();
    Route::post('sendActivateCode', 'sendActivateCode')->allowCrossDomain();
    Route::post('checkRecover', 'checkRecover')->allowCrossDomain();
    Route::post('updateAcPW', 'updateAcPW')->allowCrossDomain();
    Route::post('createEmpAc', 'createEmpAc')->allowCrossDomain();
    /***********员工业绩******/
    Route::post('submitPerformancByUuid', 'submitPerformancByUuid')->middleware('checkrequest', 5)->allowCrossDomain();
    Route::get('selectPerformanceByUuid', 'selectPerformanceByUuid')->middleware('checkrequest', 5)->allowCrossDomain();
    Route::get('selectPerformanceGoodsByUuid', 'selectPerformanceGoodsByUuid')->middleware('checkrequest', 5)->allowCrossDomain();
    Route::post('deletePerformanceByUuid', 'deletePerformanceByUuid')->middleware('checkrequest', 5)->allowCrossDomain();
    /**********员工动态 */
    //请假
    Route::get('selectEmployeeLeaveByUuid', 'selectEmployeeLeaveByUuid')->middleware('checkrequest', 5)->allowCrossDomain();
    Route::post('addEmployeeLeave', 'addEmployeeLeave')->middleware('checkrequest', 5)->allowCrossDomain();
    Route::post('recallEmployeeLeave', 'recallEmployeeLeave')->middleware('checkrequest', 5)->allowCrossDomain();
    //离职
    Route::get('selectEmployeeQuitByUuid', 'selectEmployeeQuitByUuid')->middleware('checkrequest', 5)->allowCrossDomain();
    Route::post('addEmployeeQuit', 'addEmployeeQuit')->middleware('checkrequest', 5)->allowCrossDomain();
    Route::post('recallEmployeeQuit', 'recallEmployeeQuit')->middleware('checkrequest', 5)->allowCrossDomain();

    /**********员工信息反馈 */
    Route::get('selectFeedbackByUuid', 'selectFeedbackByUuid')->middleware('checkrequest', 5)->allowCrossDomain();
    Route::post('addFeedback', 'addFeedback')->middleware('checkrequest', 5)->allowCrossDomain();
    Route::post('recallFeedback', 'recallFeedback')->middleware('checkrequest', 5)->allowCrossDomain();
})->completeMatch()->prefix('Employee/');


/**
 * 商品信息
 */

Route::group('goods', function () {
    Route::post('uploadExcel', 'uploadExcel')->middleware('checkrequest', 1)->allowCrossDomain();
    Route::post('selectGoods', 'selectGoods')->middleware('checkrequest', 1)->allowCrossDomain();
})->completeMatch()->prefix('Goods/');
