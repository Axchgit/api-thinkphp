<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 11:34:38
 * @LastEditTime: 2020-08-17 22:15:40
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\controller\Test.php
 */




declare(strict_types=1);

namespace app\controller;

// use think\facade\Request;
use think\Request;
use app\model\Admin as AdminModel;

class Test
{
    public function testOne(){
        // $admin_model = new AdminModel();
        // $data = $admin_model->saveLogcode('呵粑粑牛',12321);
        return json(true);
    }
    public function testTwo(){
        // $admin_model = new AdminModel();
        // $data = $admin_model->deleteLogcode('呵粑粑牛');
        return json(true);
    }
    //获取一个uuid
    public function testThree(){
        // $admin_model = new AdminModel();
        // $data = $admin_model->deleteLogcode('呵粑粑牛');
        return createGuid();
    }

    /**
     * @var \think\Request Request实例
     */
    protected $request;

    /**
     * 构造方法
     * @param Request $request Request对象
     * @access public
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //

        return createGuid();

        return com_create_guid();
        return AdminModel::select();
    }
    /**
     * @description: 发送登录码
     * @param {type} 
     * @return {type} 
     */
    public function sendCode()
    {
        $post =  $this->request->param();
        //知识点:PHP获得随机数
        $code = rand(111111, 999999);
        //知识点:PHP获取时间戳
        $time = time();
        //拼接时间戳与登录码
        $log_code = $code + $time * 1000000;
        $admin_model = new AdminModel();
        $res =  $admin_model->saveLogCode($post['username'], $log_code);
        $string_code = (string)$log_code;
        $code = substr($string_code, 10, 6);
        $admin_email = $admin_model->selectMail($post['username']);
        $title = '登录码';
        $content = '你好, <b>朋友</b>! <br/>这是一封来自<a href="http://www.xchtzon.top"  
        target="_blank">学创科技</a>的邮件！<br/><span>你的验证码是:' . (string)$code;
        if ($res) {
            if (sendMail($admin_email, $title, $content)) {
                $code = 200;
                $msg = '发送成功';
                // return View::fetch('success');
            } else {
                $code = 204;
                $msg = '发送失败';
            }
        } else {
            $code = 204;
            $msg = '找不到收件人';
        }
        //知识点:PHP类型转换
        // $string_code = (string)$log_code;
        // $data = [
        //     'log_code' => $log_code,
        //     //知识点:字符串截取指定片段
        //     'code' => substr($string_code,10,6),  //截取出登录码
        //     'res' => $res
        // ];
        $data = [
            'code' => $code,
            'msg' => $msg,
            "data" => ["list" => '']
        ];

        return json($data);

        // return substr($string_code,10,3);
        // return $post['username'];
    }
    // public function checkAdminLogin(){
    //     $post =  $this->request->param();
    //     $string_code = (string)$post['logcode'];
    //     $code = substr($string_code, 10, 6);
    //     $admin_model = new AdminModel();
    //     $admin_model->

    // }

}
