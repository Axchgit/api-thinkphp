<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-09-14 12:13:33
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


class Feedback extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    //员工查询业绩
    public function getFeedbackByUuid($uuid, $key, $value, $list_rows = 10, $isSimple = false, $config = '')
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

    //添加反馈
    public function saveFeedback($data)
    {

        try {
            if (!empty($data['id'])) {
                $this->update($data);
            } else {
                $this->save($data);
            }
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }

    // 删除人员信息
    public function deleteFeedbackByid($uuid, $id)
    {
        try {
            $process_status = $this->where('uuid', $uuid)->where('id', $id)->value('process_status');
            if ($process_status == 2 || $process_status == 3) {
                return '不能删除已处理数据';
            }
            //软删除
            $this->destroy($id);
            return true;
        } catch (\Exception $e) {
            return $e;
        }
        // $res = $this->save($data);
    }
    /************管理员操作 */

    //获取信息
    public function getFeedback($key, $value, $list_rows = 10, $isSimple = false, $config = '')
    {
        switch ($key) {
            case 'work_num':
                $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                    ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                    ->where('goods.' . $key, $value)
                    ->where('feedback.delete_time', null)
                    ->paginate($list_rows, $isSimple, $config);
                break;
            case 'review_status':
                $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                    ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                    ->where('goods.' . $key, $value)
                    ->where('feedback.delete_time', null)
                    ->paginate($list_rows, $isSimple, $config);
                break;
            default:
                $data = Db::view('feedback', 'id,uuid,category,matter,exact_date,process_status,handler,create_time')
                    // ->view('goods', 'order_id', 'goods.goods_id=performance.goods_id', 'LEFT')
                    ->view('employee', 'work_num,real_name', 'employee.uuid=feedback.uuid')
                    ->where('feedback.delete_time', null)
                    ->paginate($list_rows, $isSimple, $config);
        }
        if (empty($data)) {
            return false;
        } else {
            return $data;
        }
    }

    //修改/审核请假动态
    public function updateFeedback($data)
    {
        try {
            $this->update($data);
            // $this->update(['review_status' => $data['review_status'], 'id' => $data['id']]);
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }






    //over
}
