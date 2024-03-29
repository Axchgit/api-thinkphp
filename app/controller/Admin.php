<?php
/*
 * @Author: xch
 * @Date: 2020-08-17 22:03:01
 * @LastEditTime: 2021-05-27 02:24:11
 * @LastEditors: xch
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\controller\Admin.php
 * @Description: 
 */

declare(strict_types=1);

namespace app\controller;

use think\Request;

use app\model\Employee as EmployeeModel;
use app\model\EmployeeLogin as EmpLoginModel;
use app\model\Performance as PerformanceModel;
use app\model\EmployeeLeave as EmployeeLeaveModel;
use app\model\EmployeeQuit as EmployeeQuitModel;
use app\model\Feedback as FeedbackModel;
use app\model\Bulletin as BullteinModel;


use app\publicClass\DataBack;





use think\facade\Db;

class Admin extends Base
{
    /**
     * @description: 上传员工数据存入数据库
     * @param {type} 
     * @return {type} 
     */
    public function uploadExcel()
    {
        $post =  request()->param();
        $emp_model = new EmployeeModel();
        $res = $emp_model->insertEmployee($post);
        if ($res === true) {
            return $this->create('', '添加成功', 200);
        } else {
            return $this->create($res, $res, 204);
        }
    }
    //员工个人信息查询

    public function selectEmployeeInfo()
    {
        $post = request()->param();
        $emp_model = new EmployeeModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list = $emp_model->getEmployeeInfo($key, $value, $post['list_rows'], false, ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }
    //修改员工账户信息
    public function updateEmployeeInfo()
    {
        $post =  request()->param();
        $emp_model = new EmployeeModel();
        $res = $emp_model->updateEmployeeInfo($post);
        if ($res === true) {
            return $this->create('', '修改成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }
    //删除员工个人信息
    public function deleteEmployeeInfo()
    {
        $post =  request()->param();
        $emp_model = new EmployeeModel();
        $res = $emp_model->deleteEmployeeInfo($post['id']);
        if ($res === true) {
            return $this->create('', '账户信息删除成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }
    //员工账户信息查询
    public function selectEmployeeAccount()
    {
        $post = request()->param();
        $emplogin_model = new EmpLoginModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list = $emplogin_model->getEmployeeAccount($key, $value, $post['list_rows'], false, ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }

    //修改员工账户信息
    public function updateEmployeeAccount()
    {
        $post =  request()->param();
        $emplogin_model = new EmpLoginModel();
        $res = $emplogin_model->updateEmployeeAccount($post);
        if ($res === true) {
            return $this->create('', '修改成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }

    //删除员工账户信息
    public function deleteEmployeeAccount()
    {
        $post =  request()->param();
        $emplogin_model = new EmpLoginModel();
        $res = $emplogin_model->deleteEmployeeAccount($post['id']);
        if ($res === true) {
            return $this->create('', '账户信息删除成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }

    /****************业绩审核 */
    //查找所有业绩信息
    public function selectPerformance()
    {
        $post = request()->param();
        $performance_model = new PerformanceModel();
        $list_rows = !empty($post['list_rows']) ? $post['list_rows'] : '';

        $list = $performance_model->getPerformance($list_rows, ['query' => $post],$post);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }

    //审核业绩
    public function reviewPerformance(Request $request)
    {
        $post =  request()->param();
        $mid_res = $request->data;
        $emp_model = new EmployeeModel();
        $performance_model = new PerformanceModel();
        $handler = $emp_model->where('uuid', $mid_res['data']->uuid)->value('work_num');
        $post['handler'] = $handler;
        $res = $performance_model->updatePerformance($post);
        if ($res === true) {
            return $this->create('', '修改成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }

    /********************动态审核 */
    //查找请假信息
    public function selectEmployeeLeave()
    {
        $post = request()->param();
        $leave_model = new EmployeeLeaveModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list = $leave_model->getEmployeeLeave($key, $value, $post['list_rows'], false, ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }
    //审核请假动态
    public function reviewEmployeeLeave(Request $request)
    {
        $post =  request()->param();
        $mid_res = $request->data;
        $emp_model = new EmployeeModel();
        $leave_model = new EmployeeLeaveModel();
        $reviewer = $emp_model->where('uuid', $mid_res['data']->uuid)->value('work_num');
        $post['reviewer'] = $reviewer;
        $res = $leave_model->updateEmployeeLeave($post);
        // return $this->create($post, '修改成功', 200);

        if ($res === true) {
            return $this->create('', '修改成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }

    //查找离职信息
    public function selectEmployeeQuit()
    {
        $post = request()->param();
        $quit_model = new EmployeeQuitModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list = $quit_model->getEmployeeQuit($key, $value, $post['list_rows'], false, ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }
    //审核离职动态
    public function reviewEmployeeQuit(Request $request)
    {
        $post =  request()->param();
        $mid_res = $request->data;
        $emp_model = new EmployeeModel();
        $quit_model = new EmployeeQuitModel();
        $reviewer = $emp_model->where('uuid', $mid_res['data']->uuid)->value('work_num');
        $post['reviewer'] = $reviewer;
        $res = $quit_model->updateEmployeeQuit($post);
        // return $this->create($post, '修改成功', 200);
        if ($res === true) {
            return $this->create('', '修改成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }


    /****************反馈处理 */
    //查询所有反馈信息
    public function selectFeedback()
    {
        $post = request()->param();
        $feedback_model = new FeedbackModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $list = $feedback_model->getFeedback($key, $value, $post['list_rows'], false, ['query' => $post]);
        if ($list) {
            return $this->create($list, '查询成功');
        } else {
            return $this->create($list, '暂无数据', 204);
        }
    }

    //处理信息反馈
    public function reviewFeedback(Request $request)
    {
        $post =  request()->param();
        $mid_res = $request->data;
        $feedback_model = new FeedbackModel();
        $emp_model = new EmployeeModel();
        $handler = $emp_model->where('uuid', $mid_res['data']->uuid)->value('work_num');
        $post['handler'] = $handler;
        $res = $feedback_model->updateFeedback($post);
        if ($res === true) {
            return $this->create('', '修改成功', 200);
        } else {
            return $this->create($res, '失败', 204);
        }
    }



    /**************通告 */
    public function sendBulletin(Request $request)
    {
        $post = request()->param();
        $tooken_res = $request->data;
        $uuid = $tooken_res['data']->uuid;
        $post['creater_uid'] = $uuid;
        $bulletin_model = new BullteinModel();
        $res = $bulletin_model->createBulletin($post);
        if ($res === true) {
            return $this->create('', '发送成功');
        } else {
            return $this->create($res, '发送失败', 204);
        }
    }

        //获取通告
        public function viewAllBulletin()
        {
            $post = request()->param();
            // $tooken_res = $request->data;
            // $uuid = $tooken_res['data']->uuid;
            // $person_model = new PersonModel();
            // $employee_model = new EmployeeModel();
            $bulletin_model = new BullteinModel();
            // $emp_info = $employee_model->getInfoByUuid($uuid);
            $list_rows = !empty($post['list_rows']) ? $post['list_rows'] : '';
            $list = $bulletin_model->getAllBulletin($list_rows, ['query' => $post]);
            return $list;
        }

        //删除通告
        public function deleteBulletin(){
            $post = request()->param();
            $bulletin_model = new BullteinModel();
            $res = $bulletin_model->deleteById($post['id']);
            if ($res === true) {
                return $this->create('', '删除成功');
            } else {
                return $this->create($res, '删除失败', 204);
            }
        }

    /*********** */



    /**********数据库备份*********** */

    //获取备份文件列表
    public function viewBackupFile()
    {
        // $dbhost = config('database.connections.mysql.hostname');
        $post = request()->param();
        $databack = new DataBack();

        $file_path = !empty($post['file_path']) ? $post['file_path'] : config('database.backup.databack_path');
        $res = $databack->getDirContent($file_path);
        if ($res[0] !== true) {
            return $this->create('', '系统错误');
        }
        return $this->create($res[1], '成功');
    }
    //备份数据库
    public function backupSqlApi()
    {
        $post = request()->param();
        $databack = new DataBack();

        $dbname = !empty($post['dbname']) ? $post['dbname'] : 'test';
        $path = !empty($post['path']) ? $post['path'] : '+_+';
        $databack->backupSql($dbname, $path);
        return $this->create('', '成功');
    }
    //数据库恢复数据
    public function restoreSqlByBackupFile()
    {
        $post = request()->param();
        $databack = new DataBack();

        $dbname = !empty($post['dbname']) ? $post['dbname'] : config('database.connections.mysql.database');
        //替换反斜杠为斜杠
        $post['file'] = str_replace('\\', '/', $post['file']);
        $databack->restoreSql($post['file'], $dbname);
        return $this->create([$post['file'], $dbname], '成功');
    }
    //删除数据库备份文件
    public function deleteBackupFile()
    {
        $post = request()->param();
        unlink($post['file']);
        return $this->create('', '成功');
    }





    /********************* */







    //TODO:删除废弃代码
    /**********************************旧版信息查询 */
    /**
    //  * @description: 通过工号查询员工数据
    //  * @param {type} 
    //  * @return {type} 
    //  */
    // public function selectEmpByInfo($work_num = '', $real_name = '')
    // {
    //     $emp_model = new EmployeeModel();
    //     $data = $emp_model->getEmpByWrokNum($work_num, $real_name);
    //     $list = [
    //         'data' => $data
    //     ];
    //     if ($list) {
    //         return $this->create($list, '查询成功');
    //     } else {
    //         return $this->create($list, '暂无数据', 204);
    //     }
    // }
    // /**
    //  * @description: 通过权限等级查询员工数据
    //  * @param {type} 
    //  * @return {type} 
    //  */
    // public function selectEmpByRole()
    // {
    //     $post = request()->param();
    //     $emp_model = new EmployeeModel();
    //     $data = $emp_model->getEmpByRole($post['list_rows'], '', ['query' => $post], $post['role']);
    //     if ($data) {
    //         return $this->create($data, '查询成功');
    //     } else {
    //         return $this->create($data, '暂无数据', 204);
    //     }
    // }


    /***************** 员工账户信息 ********************/



    //TODO:删除废弃代码
    // /**
    //  * @description: 获取员工资料信息
    //  * @param {type} 
    //  * @return {type} 
    //  */
    // public function selectAcAll()
    // {
    //     $post = request()->param();
    //     $emplogin_model = new EmpLoginModel();
    //     $list = $emplogin_model->getEmpAc($post['list_rows'], '', ['query' => $post]);
    //     if ($list) {
    //         return $this->create($list, '查询成功');
    //     } else {
    //         return $this->create($list, '暂无数据', 204);
    //     }
    // }
    // /**
    //  * @description: 通过昵称查询员工数据
    //  * @param {type} 
    //  * @return {type} 
    //  */
    // public function selectAcByName($nick_name = '')
    // {
    //     $emplogin_model = new EmpLoginModel();
    //     $data = $emplogin_model->getEmpAcByName($nick_name);
    //     // return $data;
    //     $list = [
    //         'data' => $data
    //     ];
    //     if ($data) {
    //         return $this->create($list, '查询成功');
    //     } else {
    //         return $this->create($list, '暂无数据', 204);
    //     }
    // }
    // /**
    //  * @description: 通过权限等级查询员工资料
    //  * @param {type} 
    //  * @return {type} 
    //  */
    // public function selectAcByRole()
    // {
    //     $post = request()->param();
    //     $emplogin_model = new EmpLoginModel();
    //     $data = $emplogin_model->getEmpAcByRole($post['list_rows'], '', ['query' => $post], $post['role']);
    //     if ($data) {
    //         return $this->create($data, '查询成功');
    //     } else {
    //         return $this->create($data, '暂无数据', 204);
    //     }
    // }
    // //忘记密码-发送验证码
    // public function sendRecoverCode()
    // {
    //     $post = request()->param();
    //     $emp_model = new EmployeeModel();
    //     $emp_model->deleteEmpCode($post['work_num']);
    //     //验证码
    //     $code = rand(111111, 999999);
    //     $time = time();
    //     $time_code = (string)$time . (string)$code;
    //     //邮箱内容
    //     $title = '验证码';
    //     $content = '你好, <b>朋友</b>! <br/>这是一封来自<a href="http://www.xchtzon.top"  
    //         target="_blank">学创科技</a>的邮件！<br/><span>你正在修改你的密码,你的验证码是:' . (string)$code;
    //     $res = $emp_model->where('work_num', $post['work_num'])->where('email', $post['email'])->find();
    //     if (!empty($res)) {

    //         $res = $emp_model->saveEmpCode($post['work_num'], $time_code, $title);
    //         if ($res) {

    //             if (sendMail($post['email'], $title, $content)) {
    //                 $code = 200;
    //                 $msg = '发送成功';
    //             } else {
    //                 $code = 204;
    //                 $msg = '发送失败';
    //             }
    //         } else {
    //             $code = 204;
    //             $msg = '找不到收件人';
    //         }
    //         return $this->create('', $msg, $code);
    //     } else {
    //         return $this->create('', '用户信息有误', 204);
    //     }
    // }
    // //忘记密码-检查信息
    // public function checkRecover()
    // {
    //     $post = request()->param();
    //     $emp_uuid = EmployeeModel::where('work_num', $post['work_num'])->value('uuid');
    //     $code_info = Db::table('temp_code')->where('uuid', $emp_uuid)->find();

    //     $string_code = (string)$code_info['code'];
    //     $code = substr($string_code, 10, 6);
    //     //获取当前时间戳
    //     $now = time();
    //     //获取登录码时间戳
    //     $time = substr($string_code, 0, 10);
    //     if ($code == $post['code']) {
    //         if ($time + config("login.code_timeout") >= $now) {
    //             return $this->create(['uuid' => $emp_uuid], '成功', 200);
    //         } else {
    //             return $this->create('', '验证码超时', 201);
    //         }
    //     } else {
    //         return $this->create('', '验证码错误', 204);
    //     }
    // }
    // //忘记密码-修改
    // public function updateAcPW()
    // {
    //     $post = request()->param();
    //     $emp_login = new EmpLoginModel();
    //     $res = $emp_login->updatePW($post['uuid'], $post['password']);
    //     if ($res) {
    //         return $this->create('', '成功', 200);
    //     } else {
    //         return $this->create('', '修改失败', 204);
    //     }
    // }
    // //激活账号验证码
    // public function sendActivateCode()
    // {
    //     $post = request()->param();
    //     $emp_model = new EmployeeModel();
    //     $emp_model->deleteEmpCode($post['work_num']);
    //     $emp_uuid = $emp_model->where('work_num', $post['work_num'])->where('email', $post['email'])->value('uuid');
    //     $code = rand(111111, 999999);
    //     $time = time();
    //     $time_code = (string)$time . (string)$code;
    //     //邮箱信息
    //     $title = '验证码';
    //     $content = '你好, <b>朋友</b>! <br/>这是一封来自<a href="http://www.xchtzon.top"  
    //         target="_blank">学创科技</a>的邮件！<br/><span>你正在激活你的员工账户,你的验证码是:' . (string)$code;
    //     if (!empty($emp_uuid)) {
    //         $res = $emp_model->saveEmpCode($post['work_num'], $time_code, $title);
    //         if ($res) {
    //             if (sendMail($post['email'], $title, $content)) {
    //                 $code = 200;
    //                 $msg = '发送成功';
    //             } else {
    //                 $code = 204;
    //                 $msg = '发送失败';
    //             }
    //         } else {
    //             $code = 204;
    //             $msg = '找不到收件人';
    //         }
    //         return $this->create(['uuid' => $emp_uuid], $msg, $code);
    //     } else {
    //         return $this->create('', '用户信息有误', 204);
    //     }
    // }
    // //激活账号
    // public function createEmpAc()
    // {
    //     $post = request()->param();
    //     $emp_login = new EmpLoginModel();
    //     //验证码
    //     $code_info = Db::table('temp_code')->where('uuid', $post['uuid'])->find();
    //     $string_code = (string)$code_info['code'];
    //     $code = substr($string_code, 10, 6);
    //     //获取当前时间戳
    //     $now = time();
    //     //获取登录码时间戳
    //     $time = substr($string_code, 0, 10);
    //     if ($code == $post['code']) {
    //         if ($time + config("login.code_timeout") >= $now) {
    //             $res = $emp_login->insertEmpAc($post);
    //             $update_res = EmployeeModel::where('uuid', $post['uuid'])->save(['review_status' => 1]);
    //             if ($res && $update_res) {
    //                 return $this->create('', '激活成功', 200);
    //             } else {
    //                 return $this->create('', '激活失败,未知错误', 204);
    //             }
    //         } else {
    //             return $this->create('', '验证码超时', 201);
    //         }
    //     } else {
    //         return $this->create('', '验证码', 201);
    //     }
    // }

    // //提交业绩
    // public function submitPerformanc(Request $request)
    // {
    //     $post = request()->param();
    //     $res = $request->data;
    //     $performance_model = new PerformanceModel();
    //     $res = $performance_model->insertPerformance($res['data']->uuid, $post['goods_id']);
    //     if ($res === true) {
    //         return $this->create('', '添加成功', 200);
    //     } else {
    //         return $this->create($res, '添加失败', 204);
    //     }
    // }

    // //员工查询个人业绩
    // public function selectPerformanceByUuid(Request $request)
    // {
    //     $post =  request()->param();
    //     $res = $request->data;
    //     $per_model = new PerformanceModel();
    //     $key = !empty($post['key']) ? $post['key'] : '';
    //     $value = !empty($post['value']) ? $post['value'] : '';
    //     $list_rows = !empty($post['list_rows']) ? $post['list_rows'] : '';
    //     $data = $per_model->selectPerformance($res['data']->uuid, $key, $value, $list_rows, false, ['query' => $post]);
    //     if ($data) {
    //         return $this->create($data, '查询成功');
    //     } else {
    //         return $this->create($data, '暂无数据', 204);
    //     }
    // }

    // //员工删除个人业绩
    // public function deletePerformanceByUuuid(Request $request)
    // {
    //     $post =  request()->param();
    //     $res = $request->data;
    //     $per_model = new PerformanceModel();
    //     $res = $per_model->softDeletePerformance($res['data']->uuid, $post['id']);
    //     if ($res === true) {
    //         return $this->create('', '删除成功', 200);
    //     } else {
    //         return $this->create($res, '删除失败', 204);
    //     }
    // }

    // //员工查询个人推广商品
    // public function selectPerformanceGoodsByUuid(Request $request)
    // {
    //     $post =  request()->param();
    //     // return json($post);

    //     // return $post;
    //     $res = $request->data;
    //     $per_model = new PerformanceModel();
    //     $key = '';
    //     $value = '';
    //     // return $val;
    //     // 判断是否有查询条件
    //     $is_defined = !empty($post['goods_id']) || !empty($post['audit_status']);
    //     $is_all_derfined = !empty($post['goods_id']) && !empty($post['audit_status']);
    //     if ($is_defined) {
    //         if ($is_all_derfined) {
    //             return $this->create('', '暂时不支持同时查询', 204);
    //         }
    //         $key = !empty($post['goods_id']) ? 'goods_id' : 'audit_status';
    //         $value = $post[$key];
    //     }
    //     // $value = !empty($post['value']) ? $post['value'] : '';
    //     $list_rows = !empty($post['list_rows']) ? $post['list_rows'] : '';
    //     $data = $per_model->selectPerformanceGoods($res['data']->uuid, $key, $value, $list_rows, false, ['query' => $post]);
    //     if ($data) {
    //         return $this->create($data, '查询成功');
    //     } else {
    //         return $this->create('', '暂无数据', 204);
    //     }
    // }









    //结束
}
