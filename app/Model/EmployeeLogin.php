<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2021-04-10 18:23:18
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\Model\EmployeeLogin.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;
use app\model\Employee as EmployeeModel;


class EmployeeLogin extends Model
{
    //员工查询业绩
    public function getEmployeeAccount($key, $value, $list_rows = 10, $isSimple = false, $config = '')
    {
        switch ($key) {
            case 'nick_name':
                $data = Db::view('employee_login', 'id,uuid,nick_name,password,profile,create_time')
                    ->view('employee', 'work_num,real_name,role', 'employee_login.uuid = employee.uuid')
                    ->where($key, $value)
                    ->paginate($list_rows, $isSimple, $config);
                break;
            case 'work_num':
                $data = Db::view('employee_login', 'id,uuid,nick_name,password,profile,create_time')
                    ->view('employee', 'work_num,real_name,role', 'employee_login.uuid = employee.uuid')
                    ->where($key, $value)
                    ->paginate($list_rows, $isSimple, $config);
                break;
            default:
                $data = Db::view('employee_login', 'id,uuid,nick_name,password,profile,create_time')
                    ->view('employee', 'work_num,real_name,role', 'employee_login.uuid = employee.uuid')
                    ->where($key, $value)
                    ->paginate($list_rows, $isSimple, $config);
        }
        if (empty($data[0])) {
            return false;
        } else {
            return $data;
        }
    }
    // 修改人员信息
    public function updateEmployeeAccount($data)
    {
        try {
            $this->update($data);
            return true;
        } catch (\Exception $e) {
            return $e;
        }
        // $res = $this->save($data);
    }

    // 根据uuid修改人员信息
    public function updateEmployeeAccountByUuid($data,$uuid)
    {
        try {
            $id = $this->getAcValueByUuid($uuid,'id');
            // $this->update($data,['id'=>$id]);
            $this->update($data,['id'=>$id]);
            // return [$id,$data];
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        // $res = $this->save($data);
    }

    // 删除人员信息
    public function deleteEmployeeAccount($id)
    {
        try {
            //软删除
            $uuid = $this->where('id', $id)->value('uuid');
            $this->destroy($id);
            //更新账户激活状态
            $emp_model = new EmployeeModel();
            $employee = $emp_model->where('uuid', $uuid)->find();
            $employee->review_status = 0;
            $employee->save();
            return true;
        } catch (\Exception $e) {
            return $e;
        }
        // $res = $this->save($data);
    }

    /**
     * @description: 员工登录验证
     * @param {type} 
     * @return {type} 
     */
    public function findEmployee($name, $password)
    {
        return $this->where('nick_name', $name)->where('password', $password)->find();
    }
    /**
     * @description: 获取员工资料,分页显示
     * @param {type} 
     * @return {type} 
     */
    public function getEmpAc($list_rows, $isSimple = false, $config)
    {
        $data = $this->paginate($list_rows, $isSimple = false, $config);
        //判断是否有值
        if ($data->isEmpty()) {
            return false;
        } else {
            return $data;
        }
    }
    /**
     * @description: 通过昵称查询
     * @param {type} 
     * @return {type} 
     */
    public function getEmpAcByName($nick_name)
    {
        // if (empty($work_num)) {
        $data = $this->where('nick_name', $nick_name)->select();
        // } else if (empty($real_name)) {
        //     $data = $this->where('work_num', $work_num)->select();
        // } else {
        //     $data = $this->where('work_num', $work_num)->where('real_name', $real_name)->find();
        // }
        if (!$data) {
            return false;
        } else {
            return $data;
        }
    }
    /**
     * @description: 根据uuid查询单个信息
     * @param {type} 
     * @return {type} 
     */
    public function getInfoByUuid($emp_uuid, $value)
    {
        return Db::table('employee')->where('uuid', $emp_uuid)->value($value);
    }
        /**
     * @description: 根据uuid查询单个信息
     * @param {type} 
     * @return {type} 
     */
    public function getAcValueByUuid($emp_uuid, $value)
    {
        return $this->where('uuid', $emp_uuid)->value($value);
    }
    /**
     * @description: 通过权限查询,多个数据,用到分页
     * @param {type} 
     * @return {type} 
     */
    public function getEmpAcByRole($list_rows, $isSimple = false, $config, $role)
    {
        $data = $this->where('role', $role)->paginate($list_rows, $isSimple = false, $config);
        if ($data->isEmpty()) {
            return false;
        } else {
            return $data;
        }
    }

    public function getAcInfo($emp_uuid)
    {
        return $this->where('uuid', $emp_uuid)->select();
    }

    //通过uuid查询个人信息
    public function getAcInfoByUuid($emp_uuid)
    {
        return $this->where('uuid', $emp_uuid)->find();
    }

    public function updatePW($uuid, $new_password)
    {
        $emp =  $this->where('uuid', $uuid)->find();
        $emp->password = $new_password;
        return $emp->save();
    }

    public function insertEmpAc($post)
    {
        return $this->allowField(['nick_name', 'password', 'profile', 'uuid'])->save($post);
    }
}
