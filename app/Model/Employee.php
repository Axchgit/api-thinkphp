<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-08-17 22:41:26
 * @LastEditors: xch
 * @Description: 员工信息
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\Model\Employee.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;


class Employee extends Model
{

    //获取员工信息,分页显示
    public function getEmpInfo($list_rows,$isSimple = false ,$config){
        $data = $this->paginate($list_rows,$isSimple = false ,$config);
        //判断是否有值
        if($data->isEmpty()){
            return false;
        }else{
            return $data;
        }
    }


    
}
