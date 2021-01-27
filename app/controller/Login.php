<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 11:34:38
 * @LastEditTime: 2021-01-27 16:59:39
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\controller\Login.php
 */


declare(strict_types=1);

namespace app\controller;

// use think\facade\Request;
use think\Request;
use app\model\Admin as AdminModel;
use app\model\Employee as EmployeeModel;
use app\model\EmployeeLogin as EmployeeLoginModel;
use app\model\Auth as AuthModel;

use think\facade\Db;


class Login extends Base
{
    /**
     * 返回管理员信息
     *
     * @return \think\Response
     */
    public function selectAdminInfo()
    {
        //从请求头啊获取token
        $token =  request()->header('Authorization');
        // return $token;       
        //检查token合法性    
        $res = $this->tokenCheck($token);
        // return gettype($res);
        if ($res['code'] == 2) {
            //知识点:php连接字符串用 . 
            return $this->create('', 'token出错:' . $res['msg'], 304);
        }
        // return 123;
        //知识点:php访问对象属性:$res['data']->uuid
        $admin_info = AdminModel::where('uuid', $res['data']->uuid)->find();
        return $this->create($admin_info);
    }
    /**
     * @description: 发送登录码
     * @param {type} 
     * @return {type} 
     */
    public function sendAdminCode()
    {
        $post =  request()->param();
        //PHP获得随机数
        $code = rand(111111, 999999);
        //PHP获取时间戳
        $time = time();
        //拼接时间戳与登录码
        $log_code = $code + $time * 1000000;
        $admin_model = new AdminModel();
        //删除之前的登录码
        $res = $admin_model->deleteLogcode($post['username']);
        if ($res === false) {
            return $this->create('', '找不到该用户,请检查用户名是否正确' . $res, 204);
        }
        //保存登录码信息到临时表
        $res =  $admin_model->saveLogCode($post['username'], $log_code);
        $string_code = (string)$log_code;
        $code = substr($string_code, 10, 6);
        //查询账户对应email
        $admin_email = $admin_model->selectMail($post['username']);
        $title = '登录码';
        $content = '你好, <b>朋友</b>! <br/>这是一封来自<a href="http://www.xchtzon.top"  
        target="_blank">学创科技</a>的邮件！<br/><span>你的验证码是:' . (string)$code;
        if ($res) {
            if (sendMail($admin_email, $title, $content)) {
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
    }
    /**
     * @description: 登录验证
     * @param {type} 
     * @return {type} 
     */
    public function checkAdminLogin()
    {
        //获取请求信息
        $post =  request()->param();
        //实例化模型
        $admin_model = new AdminModel();
        //获取管理员信息
        $admin_info = $admin_model->findAdmin($post['username'], $post['password']);
        //检查是否为空
        if (!empty($admin_info)) {
            //根据管理员uuid查找登录码
            $code_info = Db::table('temp_code')->where('uuid', $admin_info['uuid'])->find();
            if (empty($code_info)) {
                return $this->create('', '验证码错误', 204);
            }
            //截取登录码
            $string_code = (string)$code_info['code'];
            $code = substr($string_code, 10, 6);
            //获取当前时间戳
            $now = time();
            //获取登录码时间戳
            $time = substr($string_code, 0, 10);
            //从数据库删除此登录码
            $delete_res = Db::table('temp_code')->where('uuid', $admin_info['uuid'])->delete();
            //判断登录码是否过期
            if ($time + config("login.code_timeout") <= $now) {
                // return $time + 60;
                return $this->create('', '验证码超时', 201);
            }
            //判断验证码是否一致
            if ($code == $post['logcode']) {
                $token = signToken($admin_info['uuid'], $admin_info['role']);
                $data = [
                    'token' => $token,
                    'uuid' => $admin_info['uuid'],
                    'role' => $admin_info['role']
                ];
                if ($delete_res) {
                    //插入登录记录
                    $records = [
                        'uuid' => $admin_info['uuid'],
                        'login_time' => time(),
                        'login_ip' => request()->host()
                    ];
                    Db::table('login_record')->insert($records);
                    //成功返回token及uuid
                    return $this->create($data, '登录成功');
                } else {
                    return $this->create('', '服务器出现了一个错误', 204);
                }
            } else {
                return $this->create('', '验证码错误', 204);
            }
        } else {
            return $this->create('', '账户或密码错误', 204);
        }
    }

    public function checkEmpLogin()
    {
        //获取请求信息
        $post =  request()->param();
        //实例化模型
        $ea_model = new EmployeeLoginModel();
        // if(!empty($post['is_phone']) && $post['isPhone'] ===true){

        // }
        //获取信息
        $ea_info = $ea_model->findEmployee($post['username'], $post['password']);
        $emp_role = $ea_model->getInfoByUuid($ea_info['uuid'], 'role');
        //检查是否为空
        if (!empty($ea_info) && !empty($emp_role)) {
            $token = signToken($ea_info['uuid'], $emp_role);
            $data = [
                'token' => $token,
                'uuid' => $ea_info['uuid'],
                'role' => $emp_role
            ];
            //添加登录记录
            $records = [
                'uuid' => $ea_info['uuid'],
                'login_time' => time(),
                'login_ip' => request()->host()
            ];
            if (Db::table('login_record')->insert($records)) {
                //成功返回token及uuid
                return $this->create($data, '登录成功');
            } else {
                return $this->create('', '未知错误', 204);
            }
        } else {
            return $this->create('', '账户或密码错误', 204);
        }
    }

    public function selectEmpInfo(Request $request)
    {
        $emp_model = new EmployeeLoginModel();
        $res = $request->data;
        // $emp_info = $emp_model->getAcInfo($res['data']->uuid);
        $emp_info = $emp_model->where('uuid', $res['data']->uuid)->find();
        // return $this->create($res['data'],'',204);

        return $this->create($emp_info);
    }

    //获取qruid
    public function getQruid()
    {
        $auth_model = new AuthModel();

        $ip = getClientRealIP();
        $fakeip = "49.74.160.84";
        $qruid = str_replace("-", "", createGuid());
        $url = "http://api.map.baidu.com/location/ip?ip=" . $fakeip . "&ak=nSxiPohfziUaCuONe4ViUP2N&coor=bd09ll";
        $address_res = httpUtil($url);
        if ($address_res['status'] === 0 && $address_res != null) {
            $address =  $address_res['content']['address'];
        } else {
            $address = '江苏省南京市';
        }
        // $qruid = createGuid();
        $res = $auth_model->createAuth($qruid, $ip, $address);
        if ($res === true) {
            return $this->create(['qruid' => $qruid, 'status' => 0]);
        } else {
            return $this->create(['status' => 1,$res], '获取验证码失败');
        }
        // return json_decode($address,true);

    }

    public function getAuthInfo($qruid)
    {
        $auth_model = new AuthModel();
        $employee_model = new EmployeeModel();
        $emp_model = new EmployeeLoginModel();

        $post =  request()->param();
        $work_num = !empty($post['work_num']) ? $post['work_num'] : '';
        $isScan = !empty($post['isScan']) ? $post['isScan'] : false;



        // return 1234;
        $auth_info = $auth_model->findAuth($qruid);
        if ($auth_info === null) {
            return $this->create('', '获取口令信息失败', 204);
        }
        if ($isScan && ($auth_info['auth_state'] === 0 || $auth_info['auth_state'] === 2)) {
            $auth_update_res = $auth_model->updateAuth($qruid, 2, $work_num);
            if ($auth_update_res !== true) {
                return $this->create($auth_info, '更新口令信息失败', 204);
            }
            $test = [
                'qruid' => $auth_info['qruid'],
                // 'auth_time' => substr($auth_info['auth_time'], 0, 10),
                'auth_time' => strtotime($auth_info['auth_time']) * 1000,

                'auth_ip' => $auth_info['auth_ip'],
                'auth_address' => $auth_info['auth_address'],
                'auth_state' => $auth_info['auth_state'],
                'work_num' => $work_num


                // 'auth_address' => '江苏南京'
            ];
            return $this->create($test, '获取成功');
        }
        if (!$isScan && $auth_info['auth_state'] === 1) {
            $emp_info = $employee_model->getEmployeeInfoByKey('work_num', $auth_info['work_num']);
            $token = signToken($emp_info['uuid'], $emp_info['role']);
            $auth_info['token'] = $token;
            $auth_info['role'] = $emp_info['role'];
            $auth_info['uuid'] = $emp_info['uuid'];
            // $auth_info['token']=$token;            
        }

        return $this->create($auth_info, '获取成功');
    }


    public function getUserInfo()
    {
        $post =  request()->param();
        $employee_model = new EmployeeModel();
        $emp_model = new EmployeeLoginModel();
        $emp_info = $employee_model->getEmployeeInfoByKey('work_num', $post['workNum']);
        $ea_info = $emp_model->getAcInfoByUuid($emp_info['uuid']);
        $user_info = [
            'work_num' => $emp_info['work_num'],
            'password' => $ea_info['password'],
            'nick_name' => $ea_info['nick_name'],
            // 'avatar' => $ea_info['avatar'],
            'avatar' => "http://phone.xchtzon.top/images/avatar/avatar_def5.jpg",

            'phone' => $emp_info['phone']

        ];

        return $this->create($user_info, '获取成功');
    }

    //手机端确认二维码登录
    public function phoneConfirmLogin($qruid)
    {
        // return $this->create('', '令牌不存在', 304);
        $post =  request()->param();
        $auth_model = new AuthModel();
        $auth_state = 3;
        $auth_info = $auth_model->findAuth($qruid);
        //判断获取二维码信息是否成功
        if ($auth_info === null) {
            return $this->create(['state' => 2], '获取口令信息失败', 204);
        }
        $auth_state = $auth_info['auth_state'];
        $auth_time_timestamp = strtotime($auth_info['auth_time']);
        //判断二维码是否超时
        // return $this->create([$auth_time_timestamp,time()], '登录码过期', 204);
        if ($auth_time_timestamp + 30 <= time()) {
            return $this->create(['state' => 3], '登录码过期', 204);
        }
        //判断二维码存储信息是否对应
        if ($post['work_num'] === $auth_info['work_num'] && ($auth_state === 0 || $auth_state === 2)) {
            $auth_update_res = $auth_model->updateAuth($qruid, 1, $auth_info['work_num']);
            if ($auth_update_res !== true) {
                return $this->create(['state' => 0], '更新口令信息失败', 204);
            }
            return $this->create(['state' => 1], '确认登录成功');
        } else {
            return $this->create(['state' => 4], '口令信息错误'.$post['work_num']===$auth_info['work_num'].'-'.$auth_state, 204);
        }
    }

    //手机端登录
    public function phoneLogin()
    {
        //获取请求信息
        $post =  request()->param();
        //实例化模型
        $ea_model = new EmployeeLoginModel();
        // $post =  request()->param();
        $employee_model = new EmployeeModel();
        // $emp_model = new EmployeeLoginModel();

        $emp_info = $employee_model->getEmployeeInfoByKey('work_num', $post['work_num']);
        $ea_all_info = $ea_model->getAcInfoByUuid($emp_info['uuid']);
        $ea_info = $ea_model->findEmployee($ea_all_info['nick_name'], $post['password']);
        //检查是否为空
        if (!empty($ea_info) && !empty($emp_info['role'])) {
            $token = signToken($ea_info['uuid'], $emp_info['role']);
            $user_info = [
                'user_token' => $token,
                // 'uuid' => $ea_info['uuid'],
                'role' => $emp_info['role'],
                'work_num' => $emp_info['work_num'],
                'password' => $ea_info['password'],
                'nick_name' => $ea_info['nick_name'],
                // 'avatar' => $ea_info['avatar'],
                'avatar' => "http://phone.xchtzon.top/images/avatar/avatar_def5.jpg",

                
                'phone' => $emp_info['phone']
    
            ];
            //添加登录记录
            $records = [
                'uuid' => $ea_info['uuid'],
                'login_time' => time(),
                'login_ip' => request()->host(),
                'login_platform' => 2
            ];
            if (Db::table('login_record')->insert($records)) {
                //成功返回token及uuid
                return $this->create($user_info, '登录成功');
            } else {
                return $this->create('', '未知错误', 204);
            }
        } else {
            return $this->create('', '账户或密码错误', 204);
        }
    }









    //over
}
