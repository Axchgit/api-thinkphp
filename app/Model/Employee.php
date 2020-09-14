<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-09-14 16:01:29
 * @LastEditors: Chenhao Xing
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
    //发送验证码
    public function saveEmpCode($work_num, $time_code, $msg = '验证码')
    {
        $emp_uuid = $this->where('work_num', $work_num)->value('uuid');
        $data = [
            'uuid' => $emp_uuid,
            'code' => $time_code,
            'msg' => $msg
        ];
        //跨表数据库操作
        return Db::table('temp_code')->insert($data);
        // $admin->code = $log_code;
    }
    
    //删除验证码
    public function deleteEmpCode($work_num)
    {
        $emp_uuid = $this->where('work_num', $work_num)->value('uuid');
        return Db::table('temp_code')->where('uuid', $emp_uuid)->delete();
    }

    /********************管理员操作 */

    //查询员工个人信息
    public function getEmployeeInfo($key, $value, $list_rows = 10, $isSimple = false, $config = '')
    {
        switch ($key) {
            case 'work_name':
                $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                break;
            case 'real_name':
                $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                break;
            case 'role':
                $data = $this->whereLike($key, $value)->paginate($list_rows, $isSimple, $config);
                break;
            default:
                $data = $this->paginate($list_rows, $isSimple, $config);
        }
        if (empty($data[0])) {
            return false;
        } else {
            return $data;
        }
    }
    // 删除人员信息
    public function deleteEmployeeInfo($id)
    {
        try {
            //软删除
            $uuid = $this->where('id', $id)->value('uuid');
            $this->destroy($id);
            //更新账户激活状态
            // $emp_model = new EmployeeModel();
            // $employee = $emp_model->where('uuid',$uuid)->find();
            // $employee->review_status = 0;
            // $employee->save();
            return true;
        } catch (\Exception $e) {
            return $e;
        }
        // $res = $this->save($data);
    }
    // 修改员工个人信息
    public function updateEmployeeInfo($data)
    {
        try {
            $this->update($data);
            return true;
        } catch (\Exception $e) {
            return $e;
        }
        // $res = $this->save($data);
    }

        //插入报表
        public function insertEmployee($dataArr)
        {
            // $gt_mode = new GoodsTempModel();
            $employee = [];
            foreach ($dataArr as $k => $v) {
                

                $employee[$k]['work_num'] = empty($v['工号']) ? '' : $v['工号'];
                $employee[$k]['real_name'] = empty($v['真实姓名']) ? '' : $v['真实姓名'];
                $employee[$k]['email'] = empty($v['邮箱']) ? '' : $v['邮箱'];
                $employee[$k]['id_photo'] = empty($v['证件照存放地址']) ? '' : $v['证件照存放地址'];
                $employee[$k]['id_card'] = empty($v['身份证号']) ? '' : $v['身份证号'];
                $employee[$k]['bank_card'] = empty($v['工资卡']) ? '' : $v['工资卡'];
                $employee[$k]['birthday'] = empty($v['生日']) ? '' : $v['生日'];
                $employee[$k]['sex'] = empty($v['性别']) ? '' : $v['性别'];
                $employee[$k]['role'] = empty($v['权限等级']) ? '' : $v['权限等级'];
                $employee[$k]['work_num'] = empty($v['工号']) ? '' : $v['工号'];
                $employee[$k]['work_num'] = empty($v['工号']) ? '' : $v['工号'];
                $employee[$k]['work_num'] = empty($v['工号']) ? '' : $v['工号'];
                $employee[$k]['work_num'] = empty($v['工号']) ? '' : $v['工号'];
            }
            // }
            Db::startTrans();
            try {
                if (!empty($goods)) {
                    // $gt_mode->limit(100)->insertAll($goods);
                    Db::table('goods_temp')->limit(100)->insertAll($goods);
    
                } else {
                    // Db::rollback();
                    return false;
                }
                //查询重复数据
                $same = Db::view('goods')
                    ->view('goods_temp', 'goods_name', 'goods.order_id = goods_temp.order_id')
                    ->select();
                //删除表里的重复数据
                foreach ($same as $k => $v) {
                    Db::table('goods')->where('order_id', $v['order_id'])->delete();
                }
                //查询临时表数据
                //知识点:查询时忽略某个字段
                $data = Db::table('goods_temp')->withoutField('id')->select()->toArray();
                if (empty($data)) {
                    // Db::rollback();
                    return '临时表数据为空';
                }
                $res = $this->limit(100)->insertAll($data);
                if ($res) {
                    Db::table('goods_temp')->delete(true);
                    Db::commit();
                    return true;
                } else {
                    // Db::rollback();
                    return '插入goods表失败'.$res;
                }
            } catch (\Exception  $e) {
                Db::rollback();
                // return '插入goods表失败';
    
                return $e;
            }
        }























    /*******************废弃代码 */
    //TODO:删除废弃代码
    //获取员工信息,分页显示
    // public function getEmpInfo($list_rows, $isSimple = false, $config)
    // {
    //     $data = $this->paginate($list_rows, $isSimple = false, $config);
    //     //判断是否有值
    //     if ($data->isEmpty()) {
    //         return false;
    //     } else {
    //         return $data;
    //     }
    // }
    // //通过uuid查询
    // public function getInfoByUuid($emp_uuid, $value)
    // {
    //     return $this->where('uuid', $emp_uuid)->value($value);
    // }
    // //通过工号查询
    // public function getInfoByWorkNum($work_num, $value)
    // {
    //     return $this->where('work_num', $work_num)->value($value);
    // }
    // //通过工号/姓名查询
    // public function getEmpByWrokNum($work_num, $real_name)
    // {
    //     if (empty($work_num)) {
    //         $data = $this->where('real_name', $real_name)->select();
    //     } else if (empty($real_name)) {
    //         $data = $this->where('work_num', $work_num)->select();
    //     } else {
    //         $data = $this->where('work_num', $work_num)->where('real_name', $real_name)->find();
    //     }
    //     if (empty($data)) {
    //         return false;
    //     } else {
    //         return $data;
    //     }
    // }
    // //通过姓名查询
    // // public function findEmpAc($work_num,$email)
    // // {

    // //     //姓名可能会有重复,使用select查询
    // //     $data = $this->where('real_name', $real_name)->select();
    // //     if ($data->isEmpty()) {
    // //         return false;
    // //     } else {
    // //         return $data;
    // //     }
    // // }
    // //通过权限查询,多个数据,用到分页
    // public function getEmpByRole($list_rows, $isSimple = false, $config, $role)
    // {
    //     $data = $this->where('role', $role)->paginate($list_rows, $isSimple = false, $config);
    //     if ($data->isEmpty()) {
    //         return false;
    //     } else {
    //         return $data;
    //     }
    // }

    /*********************** */



    //over
}
