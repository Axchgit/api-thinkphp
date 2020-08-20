<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-08-20 15:36:07
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\Model\EmployeeLogin.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;


class EmployeeLogin extends Model
{

    //员工登录验证
    public function findEmployee($name, $password)
    {
        return $this->where('nick_name', $name)->where('password', $password)->find();
    }
    /**
     * @description: 根据uuid查询单个信息
     * @param {type} 
     * @return {type} 
     */
    public function getInfoByUuid($emp_uuid,$value)
    {
        return Db::table('employee')->where('uuid',$emp_uuid)->value($value);
    }



    
}
