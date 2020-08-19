<?php
/*
 * @Author: xch
 * @Date: 2020-08-17 22:03:01
 * @LastEditTime: 2020-08-19 18:25:45
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

        //
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
    // /**
    //  * @description: 通过姓名查询员工数据
    //  * @param {type} 
    //  * @return {type} 
    //  */
    // public function selectByName($real_name){
    //     $emp_model = new EmployeeModel();
    //     $data = $emp_model->getEmpByName($real_name);
    //     if($data){
    //         return $this->create($data,'查询成功');
    //     }else{
    //         return $this->create($data,'暂无数据',204);
    //     }
    // }
    /**
     * @description: 通过权限等级查询员工数据
     * @param {type} 
     * @return {type} 
     */
    public function selectByRule(){
        $post = request()->param();
        $emp_model = new EmployeeModel();
        $data = $emp_model->getEmpByRule($post['list_rows'],'',['query'=>$post],$post['rule']);
        if($data){
            return $this->create($data,'查询成功');
        }else{
            return $this->create($data,'暂无数据',204);
        }

    }

    // /**
    //  * 保存新建的资源
    //  *
    //  * @param  \think\Request  $request
    //  * @return \think\Response
    //  */
    // public function save(Request $request)
    // {
    //     //
    // }

    // /**
    //  * 显示指定的资源
    //  *
    //  * @param  int  $id
    //  * @return \think\Response
    //  */
    // public function read($id)
    // {
    //     //
    // }

    // /**
    //  * 保存更新的资源
    //  *
    //  * @param  \think\Request  $request
    //  * @param  int  $id
    //  * @return \think\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     //
    // }

    // /**
    //  * 删除指定资源
    //  *
    //  * @param  int  $id
    //  * @return \think\Response
    //  */
    // public function delete($id)
    // {
    //     //
    // }
}
