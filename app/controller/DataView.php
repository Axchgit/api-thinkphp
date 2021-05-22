<?php
/*
 * @Author: xch
 * @Date: 2020-08-17 22:03:01
 * @LastEditTime: 2021-05-23 00:31:21
 * @LastEditors: xch
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\controller\DataView.php
 * @Description: 
 */

declare(strict_types=1);

namespace app\controller;

use think\Request;

use app\model\Goods as GoodsModel;
use app\model\Performance as PerformanceModel;

use app\model\LoginRecord as LoginRecordModel;

// use app\model\Goods as GoodsModel;


use app\model\Employee as EmployeeModel;

use think\facade\Db;
use think\facade\Cache;

class DataView extends Base
{
    //获取本月业绩(件数排行榜)
    public function PerformanceRanking()
    {
        $per_model = new PerformanceModel();
        // $employee_model = new EmployeeModel();
        $res = $per_model->sumPerformanceRankingInThisMonth();
        // foreach ($dataArr as $k => $v) {
        //     $data[$k]['name'] = $employee_model->getEmployeeValueByKey('uuid', $v['uuid'], 'real_name');
        //     $data[$k]['value'] = $v['count(uuid)'];
        // }
        return $this->create($res, '查询成功');
    }
    //获取今日业绩(件数排行榜)
    public function getPerformanceRankingWithinDay()
    {
        $per_model = new PerformanceModel();
        // $employee_model = new EmployeeModel();
        $res = $per_model->sumPerformanceRankingWithinDay();
        // foreach ($dataArr as $k => $v) {
        //     $data[$k]['name'] = $employee_model->getEmployeeValueByKey('uuid', $v['uuid'], 'real_name');
        //     $data[$k]['value'] = $v['count(uuid)'];
        // }
        return $this->create($res, '查询成功');
    }
    //获取订单金额分布
    public function OrderAmountDistribution()
    {
        $goods_model = new GoodsModel();
        $dataArr = [
            'count' => Db::table('goods')->count(),
            'list_data' =>
            [
                [
                    'name' => '0~28',
                    'value' => ($goods_model->getOrederAmountDistribution(0, 2800))
                ],
                [
                    'name' => '20~30',
                    'value' => $goods_model->getOrederAmountDistribution(2000, 3000)
                ],
                [
                    'name' => '30~40',
                    'value' => $goods_model->getOrederAmountDistribution(3000, 4000)
                ],
                [
                    'name' => '40~60',
                    'value' => $goods_model->getOrederAmountDistribution(4000, 6000)
                ],
                [
                    'name' => '60~65',
                    'value' => $goods_model->getOrederAmountDistribution(6000, 6500)
                ],
                [
                    'name' => '65~100',
                    'value' => $goods_model->getOrederAmountDistribution(6500, 10000)
                ],
                [
                    'name' => '100~',
                    'value' => $goods_model->getOrederAmountDistribution(10000, 2000000000)
                ],
                // [
                //     'name' => '200~',
                //     'value' => $goods_model->getOrederAmountDistribution(20000, 2000000000)
                // ],
            ]
        ];
        // foreach ($dataArr as $k => $v) {
        //     $data[$k]['name'] = $employee_model->getEmployeeValueByKey('uuid', $v['uuid'], 'real_name');
        //     $data[$k]['value'] = $v['count(uuid)'];
        // }
        // return $goods_model->getOrederAmountDistribution(0, 20);
        return $this->create($dataArr, '查询成功');
    }

    // //获取月度佣金排行榜
    // public function expecCommissionRanking()
    // {
    //     $per_model = new PerformanceModel();
    //     $employee_model = new EmployeeModel();
    //     $goods_model = new GoodsModel();

    //     // $sum_commission = [];

    //     $all_uuid = $per_model->getPerformanceAllUuid();
    //     foreach ($all_uuid as $k => $v) {
    //         $sum_commission = 0;

    //         $data[$k]['name'] = $employee_model->getEmployeeValueByKey('uuid', $v['uuid'], 'real_name');
    //         // $data[$k]['value'] = $v['count(uuid)'];
    //         $goods_id_list = $per_model->getGoodsIdByUuid($v['uuid']);
    //         foreach ($goods_id_list as $k1 => $v1) {
    //             // $test[$k1] = $v1;
    //             $sum_commission += $goods_model->getCountCommissionByGoodsId($v1);
    //         }
    //         // $data[$k]['goods_id_count'] = count($goods_id_list);
    //         $data[$k]['sum_commission'] = $sum_commission / 100;

    //         // break;
    //     }
    //     //知识点: 对二维关联数组进行排序
    //     $sum_commission = array_column($data, 'sum_commission');
    //     array_multisort($sum_commission, SORT_DESC, $data);
    //     return $this->create($data);
    // }


    //获取每日出单量（订单）
    public function getOrderSumByDayInFourWeek()
    {
        $goods_model = new GoodsModel();
        $res = $goods_model->countOrderSumByDayInFourWeek();
        return $this->create($res, '查询成功');
    }

    //获取月度佣金和出单量
    public function getPerformanceAndCommissionSumInMonth()
    {
        $goods_model = new GoodsModel();
        $res = $goods_model->countPerformanceAndCommissionSumInMonth();
        return $this->create($res, '查询成功');
    }


    //获取月度每天出单商品种类
    public function getGoodsNumSumInMonth()
    {
        $goods_model = new GoodsModel();
        $res = $goods_model->countGoodsNumInMonth();
        return $this->create($res, '查询成功');
    }

    //获取员工登录记录
    public function getLoginRecordByDay()
    {
        $login_record = new LoginRecordModel();
        $res = $login_record->countRecordByDay();
        return $this->create($res, '查询成功');
    }

    //获取员工本月佣金排行
    public function getCommissionRankingInThisMonth()
    {
        $per_model = new PerformanceModel();
        $res = $per_model->sumCommissionInThisMonth();
        return $this->create($res, '查询成功');
    }


    public function getTheSixNumber()
    {
        $per_model = new PerformanceModel();
        $goods_model = new GoodsModel();
        $goods_sum = $goods_model->countGoodsNumWithinDayAndMonth();
        $per_sum = $per_model->sumCommissionAndPerformanceWithinMonthAndDay();

        $res = [
            // 'goods_today' => $goods_sum[0],
            // 'goods_month' => $goods_sum[0],
            // 'per_today' => $per_sum['per_today'],
            // 'per_month' => $per_sum['per_month'],
            // 'com_today' => $per_sum['com_today'],
            // 'com_month' => $per_sum['com_month'],
            $per_sum['per_today'],

            $per_sum['com_today'],
            $goods_sum[0],

            $per_sum['per_month'],

            $per_sum['com_month'],

            $goods_sum[1],




        ];
        // return  ['per_today'=>$per_today,'per_month'=>$per_month,'com_today'=>$com_today,'com_month'=>$com_month];


        return $this->create($res, '查询成功');
    }


    //获取今天占比
    public function getPerformancePercentage(){
        $per_model = new PerformanceModel();
        $per_sum = $per_model->countPerformancePercentage();

        return $this->create($per_sum, '查询成功');
    }




    //结束
}
