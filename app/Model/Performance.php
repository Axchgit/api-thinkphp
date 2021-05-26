<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2021-05-27 02:40:33
 * @LastEditors: xch
 * @Description: 员工信息
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\Model\Performance.php
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
    public function getPerformance($list_rows = 10, $config = '', $post, $isSimple = false)
    {
        // switch ($key) {
        //     case 'goods_id':
        //         $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
        //             ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
        //             // ->where('goods.' . $key, $value)
        //             ->paginate($list_rows, $isSimple, $config);
        //         break;
        //     case 'audit_status':
        //         $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
        //             ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
        //             // ->where('goods.' . $key, $value)
        //             ->paginate($list_rows, $isSimple, $config);
        //         break;
        //     default:
        //         $data = Db::view('performance', 'id,uuid,goods_id,audit_status,create_time,handler')
        //             // ->view('goods', 'order_id', 'goods.goods_id=performance.goods_id', 'LEFT')
        //             ->view('employee', 'work_num,real_name', 'employee.uuid=performance.uuid')
        //             ->paginate($list_rows, $isSimple, $config);
        // }

        $select_post = array_diff_key($post, ["list_rows" => 0, "page" => 0]);
        $select_post_new = [];
        foreach ($select_post as $k => $v) {
            $select_post_new['performance.' . $k] = $v;
            if ($k == 'work_num') {
                unset($select_post_new['performance.' . $k]);
                $select_post_new['employee.' . $k] = $v;
            }
        }
        $data = Db::view('performance', 'id,uuid,goods_id,audit_status,create_time,handler')
            // ->view('goods', 'order_id', 'goods.goods_id=performance.goods_id', 'LEFT')
            ->view('employee', 'work_num,real_name', 'employee.uuid=performance.uuid')
            ->where($select_post_new)

            ->paginate($list_rows, $isSimple, $config);
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
    // public function getPerformanceRanking()
    // {

    //     // Db::table('score')->field('user_id,SUM(score) AS sum_score')->group('user_id')->select();
    //     // return $this->orderRaw()->field('uuid,count(uuid)')->group('uuid')->select();
    //     // return $this->field('uuid,count(uuid)')->group('uuid')->order('count(uuid)','desc')->select();
    //     return $this->field('uuid,count(uuid)')->group('uuid')->select();
    // }
    //获取业绩里所有uuid
    // public function getPerformanceAllUuid()
    // {
    //     return $this->field('uuid')->group('uuid')->select();
    // }

    //根据uuid查询每个uuid下的goods_id
    // public function getGoodsIdByUuid($uuid){
    //     return $this->where('uuid',$uuid)->column('goods_id');
    // }

    //获取本月员工业绩排名
    public function sumPerformanceRankingInThisMonth()
    {
        $first_stamp = mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y'));  //获取本月第一天时间戳
        //本月第一天到现在
        $start_time = date('Y-m-d H:i:s', $first_stamp);
        $end_time = date('Y-m-d H:i:s', strtotime('+0 days'));


        $list =  Db::table('employee')
            ->alias('a')
            ->join('performance b', 'a.uuid = b.uuid')
            ->field('real_name as name')
            ->whereBetweenTime('b.create_time', $start_time, $end_time)
            ->fieldRaw('count(goods_id) as value')
            ->order('value', 'desc')
            ->group('real_name')
            ->select();
        return $list;
    }


    //获取本月员工业绩排名
    public function sumPerformanceRankingWithinDay()
    {
        // $first_stamp = mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y'));  //获取本月第一天时间戳
        // //本月第一天到现在
        // $start_time = date('Y-m-d H:i:s', $first_stamp);
        // $end_time = date('Y-m-d H:i:s', strtotime('+0 days'));

        $now = date('Y-m-d H:i:s');

        $yesterday = date('Y-m-d H:i:s', strtotime('-1 days'));

        $list =  Db::table('employee')
            ->alias('a')
            ->join('performance b', 'a.uuid = b.uuid')
            ->field('real_name as name')
            ->whereBetweenTime('b.create_time', $yesterday, $now)
            ->fieldRaw('count(goods_id) as value')
            ->order('value', 'desc')
            ->group('real_name')
            ->select();
        return $list;
    }


    //获取本月员工佣金排行
    public function sumCommissionInThisMonth()
    {
        $first_stamp = mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y'));  //获取本月第一天时间戳
        //本月第一天到现在
        $start_time = date('Y-m-d H:i:s', $first_stamp);
        $end_time = date('Y-m-d H:i:s', strtotime('+0 days'));


        $list =  Db::table('employee')
            ->alias('a')
            ->join('performance b', 'a.uuid = b.uuid')
            ->join('goods c', 'b.goods_id = c.goods_id')

            ->field('a.real_name as name')
            //设置查询时间为从本月1号开始到现在
            ->whereBetweenTime('b.create_time', $start_time, $end_time)
            ->fieldRaw('sum(expec_commission)*0.01 as sum_commission')
            ->order('sum_commission', 'desc')
            ->group('a.real_name')
            ->select();

        return $list;
    }

    //获取业绩金额统计
    public function sumCommissionAndPerformanceWithinMonthAndDay()
    {
        $first_stamp = mktime(0, 0, 0, (int)date('m'), 1, (int)date('Y'));  //获取本月第一天时间戳

        // $id = Db::table('goods')->min('id');
        // $data = Db::table('goods')->where('id', $id)->value('payment_time');
        // $nowStamp = strtotime($data);

        $first_stamp_time = date('Y-m-d H:i:s', $first_stamp);
        // $start_time = date('Y-m-d H:i:s', $nowStamp);
        // $end_time = date('Y-m-d H:i:s', strtotime('-1 days', $nowStamp));
        $now = date('Y-m-d H:i:s');

        $yesterday = date('Y-m-d H:i:s', strtotime('-1 days'));

        $per_today = $this->whereBetweenTime('create_time', $yesterday, $now)->count();
        $per_month = $this->whereBetweenTime('create_time', $first_stamp_time, $now)->count();


        $com_today =  Db::table('performance')
            ->alias('a')
            // ->join('performance b', 'a.uuid = b.uuid')
            ->join('goods b', 'a.goods_id = b.goods_id')

            // ->field('a.real_name as name')
            ->whereBetweenTime('a.create_time', $yesterday, $now)
            ->fieldRaw('sum(expec_commission)*0.01 as sum_commission')
            // ->order('sum_commission','desc')
            // ->group('a.real_name')
            ->select();

        $com_month =  Db::table('performance')
            ->alias('a')
            // ->join('performance b', 'a.uuid = b.uuid')
            ->join('goods b', 'a.goods_id = b.goods_id')

            // ->field('a.real_name as name')
            //设置查询时间为从本月1号开始到现在
            ->whereBetweenTime('a.create_time', $first_stamp_time, $now)
            ->fieldRaw('sum(expec_commission)*0.01 as sum_commission')
            // ->order('sum_commission','desc')
            // ->group('a.real_name')
            ->select();

        // return  ['first_stamp_time'=>$first_stamp_time,'start_time'=>$start_time,'end_time'=>$end_time,'com_month'=>$com_month];

        return  ['per_today' => $per_today, 'per_month' => $per_month, 'com_today' => (float)$com_today[0]['sum_commission'], 'com_month' => (float)$com_month[0]['sum_commission']];
    }


    //所占百分比
    public function countPerformancePercentage()
    {
        $now = date('Y-m-d H:i:s');

        $yesterday = date('Y-m-d H:i:s', strtotime('-1 days'));

        $per_today = $this->whereBetweenTime('create_time', $yesterday, $now)->count();

        $percentage = $per_today / 15;
        return $percentage;
    }


    //结束
}
