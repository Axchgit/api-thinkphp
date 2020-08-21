<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-08-21 15:06:11
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\Model\Admin.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;


class Admin extends Model
{

    //管理员登录验证
    public function findAdmin($name, $password)
    {
        return $this->where('admin_name', $name)->where('admin_pass', $password)->find();
    }

    /**
     * @description: 把生成的登录码保存进数据库
     * @param {type} 
     * @return {type} 
     */
    public function saveLogcode($name, $log_code)
    {
        $admin_uuid = $this->where('admin_name',$name)->value('uuid');        
        $data = [
            'uuid' => $admin_uuid,
            'code' => $log_code,
            'msg' => '登录码'
        ];
        //知识点:跨表数据库操作
        return Db::table('temp_code')->insert($data);
        // $admin->code = $log_code;
    }
        /**
     * @description: 删除此用户之前的登录码
     * @param {type} 
     * @return {type} 
     */
    public function deleteLogcode($name)
    {

        $admin_uuid = $this->where('admin_name',$name)->value('uuid');
        // $res = Db::table('temp_code')->where('uuid',$admin_uuid)->selectOrFail();
        // if($res){
        //     return Db::table('temp_code')->where('uuid',$admin_uuid)->delete();;
        // }else{
        //     return true;
        // }        
        // 知识点:跨表数据库操作
        return Db::table('temp_code')->where('uuid',$admin_uuid)->delete();
        // $admin->code = $log_code;
    }

    /**
     * @description: 查询管理员邮箱
     * @param {type} 
     * @return {type} 
     */
    public function selectMail($name)
    {

        return $this->where('admin_name', $name)->value('admin_email');
    }
}
