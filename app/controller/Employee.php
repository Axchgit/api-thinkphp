<?php
/*
 * @Author: xch
 * @Date: 2020-08-17 22:03:01
 * @LastEditTime: 2020-08-21 15:08:52
 * @LastEditors: xch
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\controller\Employee.php
 * @Description: 
 */
declare (strict_types = 1);

namespace app\controller;

use think\Request;

use app\model\Employee as EmployeeModel;

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
        $list = $emp_model->getEmpInfo($post['list_rows'],'',['query'=>$post]);
        if($list){
            return $this->create($list,'查询成功');
        }else{
            return $this->create($list,'暂无数据',204);
        }
    }
    /**
     * @description: 通过工号查询员工数据
     * @param {type} 
     * @return {type} 
     */
    public function selectByInfo($work_num = '',$real_name = ''){
        $emp_model = new EmployeeModel();
        $data = $emp_model->getEmpByWrokNum($work_num,$real_name);
        $list = [
            'data' =>$data
        ];
        if($data){
            return $this->create($list,'查询成功');
        }else{
            return $this->create($list,'暂无数据',204);
        }
    }
    /**
     * @description: 通过权限等级查询员工数据
     * @param {type} 
     * @return {type} 
     */
    public function selectByRole(){
        $post = request()->param();
        $emp_model = new EmployeeModel();
        $data = $emp_model->getEmpByRole($post['list_rows'],'',['query'=>$post],$post['role']);
        if($data){
            return $this->create($data,'查询成功');
        }else{
            return $this->create($data,'暂无数据',204);
        }
    }



    
}
