<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-08-21 15:05:40
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
    public function getEmpInfo($list_rows, $isSimple = false, $config)
    {
        $data = $this->paginate($list_rows, $isSimple = false, $config);
        //判断是否有值
        if ($data->isEmpty()) {
            return false;
        } else {
            return $data;
        }
    }
    //通过工号/姓名查询
    public function getEmpByWrokNum($work_num, $real_name)
    {
        if (empty($work_num)) {
            $data = $this->where('real_name', $real_name)->select();
        } else if (empty($real_name)) {
            $data = $this->where('work_num', $work_num)->select();
        } else {
            $data = $this->where('work_num', $work_num)->where('real_name', $real_name)->find();
        }
        if (empty($data)) {
            return false;
        } else {
            return $data;
        }
    }
    // //通过姓名查询
    // public function getEmpByName($real_name)
    // {
    //     //姓名可能会有重复,使用select查询
    //     $data = $this->where('real_name', $real_name)->select();
    //     if ($data->isEmpty()) {
    //         return false;
    //     } else {
    //         return $data;
    //     }
    // }
    //通过权限查询,多个数据,用到分页
    public function getEmpByRole($list_rows, $isSimple = false, $config, $role)
    {
        $data = $this->where('role', $role)->paginate($list_rows, $isSimple = false, $config);
        if ($data->isEmpty()) {
            return false;
        } else {
            return $data;
        }
    }
}
