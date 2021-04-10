<?php
/*
 * @Description: 
 * @Author: xch
 * @Date: 2020-09-06 03:00:08
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\controller\Index.php
 * @LastEditTime: 2021-04-10 18:23:28
 * @LastEditors: xch
 */

namespace app\controller;

use app\BaseController;
use think\Request;

use app\model\LoginRecord as LoginRecordModel;
use app\model\EmployeeLogin as EmployeeLoginModel;



class Index extends Base
{

    //获取账户资料
    public function getProfile(Request $request)
    {
        $tooken_res = $request->data;
        $uuid = $tooken_res['data']->uuid;
        // return $number;

        // $person_model = new PersonModel();
        $lg_model = new LoginRecordModel();
        $el_model = new EmployeeLoginModel();

        $el_info = $el_model->getAcInfoByUuid($uuid);
        $login_record = $lg_model->selectRecord($uuid);
        return $this->create(['login_record' => $login_record, 'el_info' => $el_info], '查询成功');
    }

    //上传头像
    public function uploadAvatar(Request $request)
    {
        // return true;
        $post = request()->param();
        // return $this->create('', $post, 204);
        $file = request()->file('img');
        // return $this->create($file, '上传成功', 200);

        $tooken_res = $request->data;
        $uuid = $tooken_res['data']->uuid;
        // $pa_model = new PersonAccountModel();
        $el_model = new EmployeeLoginModel();


        try {
            validate(['file' => ['fileSize:1024000', 'fileExt:jpg,png,gif']])->check(['file' => $file]);
            $savename = \think\facade\Filesystem::disk('avatar')->putFile('avatar', $file, 'md5');
            $res = $el_model->updateEmployeeAccountByUuid(['avatar' => $savename],$uuid);
            if ($res === true) {
                return $this->create($savename, '上传成功', 200);
            }
            return $this->create($res, '修改头像失败，数据库出错', 204);
        } catch (\think\exception\ValidateException $e) {
            return $this->create('', $e->getMessage(), 204);
        }
    }


    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V' . \think\facade\App::version() . '<br/><span style="font-size:30px;">14载初心不改 - 你值得信赖的PHP框架</span></p><span style="font-size:25px;">[ V6.0 版本由 <a href="https://www.yisu.com/" target="yisu">亿速云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ee9b1aa918103c4fc"></think>';
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
