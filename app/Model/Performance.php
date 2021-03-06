<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-09-20 12:25:30
 * @LastEditors: Chenhao Xing
 * @Description: 员工信息
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\Model\Performance.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Db;


class Performance extends Model
{
    // TODO:软删除
    // use SoftDelete;
    // protected $deleteTime = 'delete_time';

    //管理员查询业绩
    public function getPerformance($key, $value, $list_rows = 10, $isSimple = false, $config = '')
    {
        switch ($key) {
            case 'goods_id':
                $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                    ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                    ->where('goods.' . $key, $value)
                    ->paginate($list_rows, $isSimple, $config);
                break;
            case 'audit_status':
                $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                    ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                    ->where('goods.' . $key, $value)
                    ->paginate($list_rows, $isSimple, $config);
                break;
            default:
                $data = Db::view('performance', 'id,uuid,goods_id,audit_status,create_time,handler')
                    // ->view('goods', 'order_id', 'goods.goods_id=performance.goods_id', 'LEFT')
                    ->view('employee', 'work_num,real_name', 'employee.uuid=performance.uuid')
                    ->paginate($list_rows, $isSimple, $config);
        }
        if (empty($data)) {
            return false;
        } else {
            return $data;
        }
    }
    //员工提交业绩
    public function insertPerformanceByUuid($uuid, $goods_id)
    {
        try {
            $this->save(['uuid' => $uuid, 'goods_id' => $goods_id]);
            return true;
        } catch (\Exception  $e) {
            if ($e->getCode() == 10501) {
                return '不能重复添加商品';
            } else {
                return '错误码' . $e->getCode();
            }
        }
    }

    //员工查询业绩
    public function getPerformanceByUuid($uuid, $key, $value, $list_rows = 10, $isSimple = false, $config = '')
    {
        switch ($key) {
                // case 'order_id':
                //     $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                //     break;
            case 'goods_id':
                $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                break;
            case 'audit_status':
                $data = $this->where('uuid', $uuid)->where($key, $value)->paginate($list_rows, $isSimple, $config);
                break;
                // case 'audit_status':
                //     $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                //     break;
                // case 'shop_name':
                //     $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                //     break;
            default:
                $data = $this->where('uuid', $uuid)->paginate($list_rows, $isSimple, $config);
                // $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                //     ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                //     // ->view('Score', 'score', 'Score.user_id=Profile.id')
                //     ->where('uuid', $uuid)
                //     ->paginate($list_rows, $isSimple, $config);
        }
        if (empty($data)) {
            return false;
        } else {
            return $data;
        }
    }

    public function softDeletePerformanceByUuid($uuid = '', $id)
    {
        try {
            $audit_status = $this->where('uuid', $uuid)->where('id', $id)->value('audit_status');
            if ($audit_status == 2 || $audit_status == 3) {
                return '不能删除已审核数据';
            }
            $this->where('uuid', $uuid)->where('id', $id)->delete();
            return true;
        } catch (\Exception  $e) {
            return $e;
        }
    }

    //员工查询推广商品
    public function getPerformanceGoodsByUuid($uuid, $key, $value, $list_rows = 10, $isSimple = false, $config = '')
    {
        switch ($key) {
                // case 'order_id':
                //     $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                //     break;
            case 'goods_id':
                // $data = $this->where('uuid', $uuid)->where($key, $value)->paginate($list_rows, $isSimple, $config);
                $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                    ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                    // ->view('Score', 'score', 'Score.user_id=Profile.id')
                    ->where('uuid', $uuid)
                    ->where('goods.' . $key, $value)
                    ->paginate($list_rows, $isSimple, $config);
                break;
            case 'audit_status':
                // $data = $this->where('uuid', $uuid)->where($key, $value)->paginate($list_rows, $isSimple, $config);
                $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                    ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                    // ->view('Score', 'score', 'Score.user_id=Profile.id')
                    ->where('uuid', $uuid)
                    ->where('performance.' . $key, $value)
                    ->paginate($list_rows, $isSimple, $config);
                break;
                // case 'audit_status':
                //     $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                //     break;
                // case 'shop_name':
                //     $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                //     break;
            default:
                // $data = $this->where('uuid', $uuid)->paginate($list_rows, $isSimple, $config);
                $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                    ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                    // ->view('Score', 'score', 'Score.user_id=Profile.id')
                    ->where('uuid', $uuid)
                    ->paginate($list_rows, $isSimple, $config);
        }
        if (empty($data)) {
            return false;
        } else {
            return $data;
        }
    }

    //修改/审核业绩
    public function updatePerformance($data)
    {
        try {
            $this->update($data);
            return true;
        } catch (\Exception $e) {
            return $e;
        }
    }


    /**************DataView方法 */

    //获取业绩排名
    public function getPerformanceRanking()
    {

        // Db::table('score')->field('user_id,SUM(score) AS sum_score')->group('user_id')->select();
        // return $this->orderRaw()->field('uuid,count(uuid)')->group('uuid')->select();
        // return $this->field('uuid,count(uuid)')->group('uuid')->order('count(uuid)','desc')->select();
        return $this->field('uuid,count(uuid)')->group('uuid')->select();
    }
    //获取业绩里所有uuid
    public function getPerformanceAllUuid()
    {
        return $this->field('uuid')->group('uuid')->select();
    }

    //根据uuid查询每个uuid下的goods_id
    public function getGoodsIdByUuid($uuid){
        return $this->where('uuid',$uuid)->column('goods_id');
    }




    //结束
}
