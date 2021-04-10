<?php
/*
 * @Author: xch
 * @Date: 2020-08-17 22:03:01
 * @LastEditTime: 2021-04-11 00:13:05
 * @LastEditors: xch
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\controller\Employee.php
 * @Description: 
 */

declare(strict_types=1);

namespace app\controller;

use think\Request;

use app\model\Employee as EmployeeModel;
use app\model\EmployeeLogin as EmployeeLoginModel;
use app\model\EmployeeLeave as EmployeeLeaveModel;
use app\model\EmployeeQuit as EmployeeQuitModel;
use app\model\TempCode as TempCodeModel;
use app\model\Feedback as FeedbackModel;


use app\model\Performance as PerformanceModel;

use think\facade\Db;

class Employee extends Base
{
    //员工个人信息修改
    public function updateEmployeeAccountInfo(Request $request)
    {
        $post = request()->param();

        $tooken_res = $request->data;
        $uuid = $tooken_res['data']->uuid;

        $el_model = new EmployeeLoginModel();
        $res = $el_model->updateEmployeeAccountByUuid($post, $uuid);
        if ($res === true) {
            return $this->create('', '修改成功');
        } else {
            return $this->create($res, '修改失败', 204);
        }
    }
    //个人信息修改-修改密码
    public function updateEmployeeAccountPassword(Request $request)
    {
        $post = request()->param();
        $tooken_res = $request->data;
        $uuid = $tooken_res['data']->uuid;
        $code_model = new TempCodeModel();
        $el_model = new EmployeeLoginModel();


        $code_info = $code_model->getCodeInfoByUuid($uuid);
        // $code_info = Db::table('temp_code')->where('uuid', $uuid)->find();

        $string_code = (string)$code_info['code'];
        $code = substr($string_code, 10, 6);
        //获取当前时间戳
        $now = time();
        //获取登录码时间戳
        $time = substr($string_code, 0, 10);
        if ($code == $post['email_code']) {
            if ($time + config("login.code_timeout") >= $now) {
                //删除验证码
                $code_model->deleteCode($uuid);
                $update_res = $el_model->updateEmployeeAccountByUuid($post, $uuid);
                if ($update_res === true) {
                    return $this->create('', '修改成功');
                } else {
                    return $this->create($update_res, '修改失败', 204);
                }
                // return $this->create(['uuid' => $uuid], '成功', 200);
            } else {
                return $this->create('', '验证码超时', 201);
            }
        } else {
            return $this->create('', '验证码错误', 204);
        }
    }



    //忘记密码-发送验证码
    public function sendRecoverCode()
    {
        $post = request()->param();
        $emp_model = new EmployeeModel();
        $emp_model->deleteEmpCode($post['work_num']);
        //验证码
        $code = rand(111111, 999999);
        $time = time();
        $time_code = (string)$time . (string)$code;
        //邮箱内容
        $title = '验证码';
        $content = '你好, <b>朋友</b>! <br/>这是一封来自<a href="http://www.xchtzon.top"  
            target="_blank">学创科技</a>的邮件！<br/><span>你正在修改你的密码,你的验证码是:' . (string)$code;
        $res = $emp_model->where('work_num', $post['work_num'])->where('email', $post['email'])->find();
        if (!empty($res)) {

            $res = $emp_model->saveEmpCode($post['work_num'], $time_code, $title);
            if ($res) {

                if (sendMail($post['email'], $title, $content)) {
                    $code = 200;
                    $msg = '发送成功';
                } else {
                    $code = 204;
                    $msg = '发送失败';
                }
            } else {
                $code = 204;
                $msg = '找不到收件人';
            }
            return $this->create('', $msg, $code);
        } else {
            return $this->create('', '用户信息有误', 204);
        }
    }
    //忘记密码-检查信息
    public function checkRecover()
    {
        $post = request()->param();
        $emp_uuid = EmployeeModel::where('work_num', $post['work_num'])->value('uuid');
        $code_info = Db::table('temp_code')->where('uuid', $emp_uuid)->find();

        $string_code = (string)$code_info['code'];
        $code = substr($string_code, 10, 6);
        //获取当前时间戳
        $now = time();
        //获取登录码时间戳
        $time = substr($string_code, 0, 10);
        if ($code == $post['code']) {
            if ($time + config("login.code_timeout") >= $now) {
                return $this->create(['uuid' => $emp_uuid], '成功', 200);
            } else {
                return $this->create('', '验证码超时', 201);
            }
        } else {
            return $this->create('', '验证码错误', 204);
        }
    }
    //忘记密码-修改
    public function updateAcPW()
    {
        $post = request()->param();
        $emp_login = new EmployeeLoginModel();
        $res = $emp_login->updatePW($post['uuid'], $post['password']);
        if ($res) {
            return $this->create('', '成功', 200);
        } else {
            return $this->create('', '修改失败', 204);
        }
    }

    //激活账号验证码
    public function sendActivateCode()
    {
        $post = request()->param();
        $emp_model = new EmployeeModel();
        $emp_model->deleteEmpCode($post['work_num']);
        $emp_uuid = $emp_model->where('work_num', $post['work_num'])->where('email', $post['email'])->value('uuid');
        $code = rand(111111, 999999);
        $time = time();
        $time_code = (string)$time . (string)$code;
        //邮箱信息
        $title = '验证码';
        $content = '你好, <b>朋友</b>! <br/>这是一封来自<a href="http://www.xchtzon.top"  
            target="_blank">学创科技</a>的邮件！<br/><span>你正在激活你的员工账户,你的验证码是:' . (string)$code;
        if (!empty($emp_uuid)) {
            $res = $emp_model->saveEmpCode($post['work_num'], $time_code, $title);
            if ($res) {
                if (sendMail($post['email'], $title, $content)) {
                    $code = 200;
                    $msg = '发送成功';
                } else {
                    $code = 204;
                    $msg = '发送失败';
                }
            } else {
                $code = 204;
                $msg = '找不到收件人';
            }
            return $this->create(['uuid' => $emp_uuid], $msg, $code);
        } else {
            return $this->create('', '用户信息有误', 204);
        }
    }
    //激活账号
    public function createEmpAc()
    {
        $post = request()->param();
        $emp_login = new EmployeeLoginModel();
        //验证码
        $code_info = Db::table('temp_code')->where('uuid', $post['uuid'])->find();
        $string_code = (string)$code_info['code'];
        $code = substr($string_code, 10, 6);
        //获取当前时间戳
        $now = time();
        //获取登录码时间戳
        $time = substr($string_code, 0, 10);
        if ($code == $post['code']) {
            if ($time + config("login.code_timeout") >= $now) {
                $res = $emp_login->insertEmpAc($post);
                $update_res = EmployeeModel::where('uuid', $post['uuid'])->save(['review_status' => 1]);
                if ($res && $update_res) {
                    return $this->create('', '激活成功', 200);
                } else {
                    return $this->create('', '激活失败,未知错误', 204);
                }
            } else {
                return $this->create('', '验证码超时', 201);
            }
        } else {
            return $this->create('', '验证码', 201);
        }
    }

    //提交业绩
    public function submitPerformancByUuid(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        $performance_model = new PerformanceModel();
        $res = $performance_model->insertPerformanceByUuid($res['data']->uuid, $post['goods_id']);
        if ($res === true) {
            return $this->create('', '添加成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }

    //员工查询个人业绩
    public function selectPerformanceByUuid(Request $request)
    {
        $post =  request()->param();
        $res = $request->data;
        $per_model = new PerformanceModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list_rows = !empty($post['list_rows']) ? $post['list_rows'] : '';
        $data = $per_model->getPerformanceByUuid($res['data']->uuid, $key, $value, $list_rows, false, ['query' => $post]);
        if ($data) {
            return $this->create($data, '查询成功');
        } else {
            return $this->create($data, '暂无数据', 204);
        }
    }

    //员工删除个人业绩
    public function deletePerformanceByUuid(Request $request)
    {
        $post =  request()->param();
        $res = $request->data;
        $per_model = new PerformanceModel();
        $res = $per_model->softDeletePerformanceByUuid($res['data']->uuid, $post['id']);
        if ($res === true) {
            return $this->create('', '删除成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }

    //员工查询个人推广商品
    public function selectPerformanceGoodsByUuid(Request $request)
    {
        $post =  request()->param();
        // return json($post);

        // return $post;
        $res = $request->data;
        $per_model = new PerformanceModel();
        $key = '';
        $value = '';
        // return $val;
        // 判断是否有查询条件
        $is_defined = !empty($post['goods_id']) || !empty($post['audit_status']);
        $is_all_derfined = !empty($post['goods_id']) && !empty($post['audit_status']);
        if ($is_defined) {
            if ($is_all_derfined) {
                return $this->create('', '暂时不支持同时查询', 204);
            }
            $key = !empty($post['goods_id']) ? 'goods_id' : 'audit_status';
            $value = $post[$key];
        }
        // $value = !empty($post['value']) ? $post['value'] : '';
        $list_rows = !empty($post['list_rows']) ? $post['list_rows'] : '';
        $data = $per_model->getPerformanceGoodsByUuid($res['data']->uuid, $key, $value, $list_rows, false, ['query' => $post]);
        if ($data) {
            return $this->create($data, '查询成功');
        } else {
            return $this->create('', '暂无数据', 204);
        }
    }

    /*************************员工动态 */
    //员工请假
    public function selectEmployeeLeaveByUuid(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        // return json($res);
        $emp_leave_model = new EmployeeLeaveModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list = $emp_leave_model->getEmployeeLeaveByUuid($res['data']->uuid, $key, $value, $post['list_rows'], false, ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }
    //添加请假请求
    public function addEmployeeLeave(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        $post['uuid'] = $res['data']->uuid;
        $emp_leave_model = new EmployeeLeaveModel();
        $res = $emp_leave_model->saveEmployeeLeave($post);
        if ($res === true) {
            return $this->create('', '添加成功', 200);
        } else {
            return $this->create('', '添加失败', 204);
        }
    }
    //员工撤回请假请求
    public function recallEmployeeLeave(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        $emp_leave_model = new EmployeeLeaveModel();
        $res = $emp_leave_model->deleteEmployeeLeaveByid($res['data']->uuid, $post['id']);
        if ($res === true) {
            return $this->create('', '删除成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }
    //员工离职
    public function selectEmployeeQuitByUuid(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        // return json($res);
        $emp_quit_model = new EmployeeQuitModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list = $emp_quit_model->getEmployeeQuitByUuid($res['data']->uuid, $key, $value, $post['list_rows'], false, ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 200);
        }
    }
    //添加离职请求
    public function addEmployeeQuit(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        $post['uuid'] = $res['data']->uuid;
        $emp_quit_model = new EmployeeQuitModel();
        $res = $emp_quit_model->saveEmployeeQuit($post);
        if ($res === true) {
            return $this->create('', '添加成功', 200);
        } else {
            return $this->create($res, '添加失败', 204);
        }
    }
    //员工撤回请假请求
    public function recallEmployeeQuit(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        $emp_quit_model = new EmployeeQuitModel();
        $res = $emp_quit_model->deleteEmployeeQuitByid($res['data']->uuid, $post['id']);
        if ($res === true) {
            return $this->create('', '删除成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }

    /************************员工反馈 */

    public function selectFeedbackByUuid(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        // return json($res);
        $feedback_model = new FeedbackModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list = $feedback_model->getFeedbackByUuid($res['data']->uuid, $key, $value, $post['list_rows'], false, ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 200);
        }
    }

    //添加或修改反馈
    public function addFeedback(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        $post['uuid'] = $res['data']->uuid;
        $feedback_model = new FeedbackModel();
        $res = $feedback_model->saveFeedback($post);
        if ($res === true) {
            return $this->create('', '添加成功', 200);
        } else {
            return $this->create($res, '添加失败', 204);
        }
    }

    //员工撤回反馈
    public function recallFeedback(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        $feedback_model = new FeedbackModel();
        $res = $feedback_model->deleteFeedbackByid($res['data']->uuid, $post['id']);
        if ($res === true) {
            return $this->create('', '删除成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }






































    /********************************old管理员操作 */
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function selectAll()
    {
        $post = request()->param();
        $emp_model = new EmployeeModel();
        $list = $emp_model->getEmpInfo($post['list_rows'], '', ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }
    /**
     * @description: 通过工号查询员工数据
     * @param {type} 
     * @return {type} 
     */
    public function selectByInfo($work_num = '', $real_name = '')
    {
        $emp_model = new EmployeeModel();
        $data = $emp_model->getEmpByWrokNum($work_num, $real_name);
        $list = [
            'data' => $data
        ];
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }
    /**
     * @description: 通过权限等级查询员工数据
     * @param {type} 
     * @return {type} 
     */
    public function selectByRole()
    {
        $post = request()->param();
        $emp_model = new EmployeeModel();
        $data = $emp_model->getEmpByRole($post['list_rows'], '', ['query' => $post], $post['role']);
        if ($data) {
            return $this->create($data, '查询成功');
        } else {
            return $this->create($data, '暂无数据', 204);
        }
    }


    /***************** 员工账户信息 ********************/


    /**
     * @description: 获取员工资料信息
     * @param {type} 
     * @return {type} 
     */
    public function selectAcAll()
    {
        $post = request()->param();
        $emplogin_model = new EmployeeLoginModel();
        $list = $emplogin_model->getEmpAc($post['list_rows'], '', ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }
    /**
     * @description: 通过昵称查询员工数据
     * @param {type} 
     * @return {type} 
     */
    public function selectAcByName($nick_name = '')
    {
        $emplogin_model = new EmployeeLoginModel();
        $data = $emplogin_model->getEmpAcByName($nick_name);
        // return $data;
        $list = [
            'data' => $data
        ];
        if ($data) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }
    /**
     * @description: 通过权限等级查询员工资料
     * @param {type} 
     * @return {type} 
     */
    public function selectAcByRole()
    {
        $post = request()->param();
        $emplogin_model = new EmployeeLoginModel();
        $data = $emplogin_model->getEmpAcByRole($post['list_rows'], '', ['query' => $post], $post['role']);
        if ($data) {
            return $this->create($data, '查询成功');
        } else {
            return $this->create($data, '暂无数据', 204);
        }
    }

    /*********************************/

    //结束
}
