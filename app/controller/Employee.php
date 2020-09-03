<?php
/*
 * @Author: xch
 * @Date: 2020-08-17 22:03:01
 * @LastEditTime: 2020-09-04 02:31:32
 * @LastEditors: xch
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\controller\Employee.php
 * @Description: 
 */

declare(strict_types=1);

namespace app\controller;

use think\Request;

use app\model\Employee as EmployeeModel;
use app\model\EmployeeLogin as EmpLoginModel;
use app\model\Performance as PerformanceModel;

use think\facade\Db;

class Employee extends Base
{
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
        $emplogin_model = new EmpLoginModel();
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
        $emplogin_model = new EmpLoginModel();
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
        $emplogin_model = new EmpLoginModel();
        $data = $emplogin_model->getEmpAcByRole($post['list_rows'], '', ['query' => $post], $post['role']);
        if ($data) {
            return $this->create($data, '查询成功');
        } else {
            return $this->create($data, '暂无数据', 204);
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
        $emp_login = new EmpLoginModel();
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
        $emp_login = new EmpLoginModel();
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
    public function submitPerformanc(Request $request)
    {
        $post = request()->param();
        $res = $request->data;
        $performance_model = new PerformanceModel();
        $res = $performance_model->insertPerformance($res['data']->uuid, $post['goods_id']);
        if ($res === true) {
            return $this->create('', '添加成功', 200);
        } else {
            return $this->create($res, '添加失败', 204);
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
        $data = $per_model->selectPerformance($res['data']->uuid, $key, $value, $list_rows, false, ['query' => $post]);
        if ($data) {
            return $this->create($data, '查询成功');
        } else {
            return $this->create($data, '暂无数据', 204);
        }
    }

    //员工删除个人业绩
    public function deletePerformanceByUuuid(Request $request)
    {
        $post =  request()->param();
        $res = $request->data;
        $per_model = new PerformanceModel();
        $res = $per_model->softDeletePerformance($res['data']->uuid, $post['id']);
        if ($res === true) {
            return $this->create('', '删除成功', 200);
        } else {
            return $this->create($res, '删除失败', 204);
        }
    }

    //员工查询个人推广商品
    public function selectPerformanceGoodsByUuid(Request $request)
    {
        $post =  request()->param();
        $res = $request->data;
        $per_model = new PerformanceModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list_rows = !empty($post['list_rows']) ? $post['list_rows'] : '';
        $data = $per_model->selectPerformanceGoods($res['data']->uuid, $key, $value, $list_rows, false, ['query' => $post]);
        if ($data) {
            return $this->create($data, '查询成功');
        } else {
            return $this->create($data, '暂无数据', 204);
        }
    }









    //结束
}
