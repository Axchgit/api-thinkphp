<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-09-13 12:25:43
 * @LastEditors: Chenhao Xing
 * @Description: 员工信息
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\Model\Employee.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;
use think\model\concern\SoftDelete;


class EmployeeQuit extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    //员工查询业绩
    public function getEmployeeQuitByUuid($uuid, $key, $value, $list_rows = 10, $isSimple = false, $config = '')
    {
        switch ($key) {
            case 'review_satatus':
                $data = $this->where($key, $value)->where('uuid', $uuid)->paginate($list_rows, $isSimple, $config);
                break;
            case 'category':
                $data = $this->where($key, $value)->where('uuid', $uuid)->paginate($list_rows, $isSimple, $config);
                break;
            default:
                $data = $this->where('uuid', $uuid)->paginate($list_rows, $isSimple, $config);
        }
        if (empty($data[0])) {
            return false;
        } else {
            return $data;
        }
    }

    //添加leave
    public function saveEmployeeQuit($data)
    {
        try {
            $this->save($data);
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }

    // 删除人员信息
    public function deleteEmployeeQuitByid($uuid, $id)
    {
        try {
            $review_status = $this->where('uuid', $uuid)->where('id', $id)->value('review_status');
            if ($review_status == 2) {
                return '不能删除已通过审核数据';
            }
            //软删除
            $this->destroy($id);
            return true;
        } catch (\Exception $e) {
            return $e;
        }
        // $res = $this->save($data);
    }





    //over
}
