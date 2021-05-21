<?php
/*
 * @Author: xch
 * @Date: 2020-08-17 22:03:01
 * @LastEditTime: 2021-05-22 02:19:49
 * @LastEditors: xch
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\controller\DataView.php
 * @Description: 
 */

declare(strict_types=1);

namespace app\controller;

use think\Request;

use app\model\Goods as GoodsModel;
use app\model\Performance as PerformanceModel;
// use app\model\Goods as GoodsModel;


use app\model\Employee as EmployeeModel;

use think\facade\Db;
use think\facade\Cache;

class DataView extends Base
{
    //获取业绩(件数排行榜)
    public function PerformanceRanking()
    {
        $per_model = new PerformanceModel();
        $employee_model = new EmployeeModel();
        $dataArr = $per_model->getPerformanceRanking();
        foreach ($dataArr as $k => $v) {
            $data[$k]['name'] = $employee_model->getEmployeeValueByKey('uuid', $v['uuid'], 'real_name');
            $data[$k]['value'] = $v['count(uuid)'];
        }
        return $this->create($data, '查询成功');
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
                    'name' => '0~20',
                    'value' => $goods_model->getOrederAmountDistribution(0, 2000)
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
                    'name' => '40~50',
                    'value' => $goods_model->getOrederAmountDistribution(4000, 5000)
                ],
                [
                    'name' => '50~70',
                    'value' => $goods_model->getOrederAmountDistribution(5000, 7000)
                ],
                [
                    'name' => '70~100',
                    'value' => $goods_model->getOrederAmountDistribution(7000, 10000)
                ],
                [
                    'name' => '100~200',
                    'value' => $goods_model->getOrederAmountDistribution(10000, 20000)
                ],
                [
                    'name' => '200~',
                    'value' => $goods_model->getOrederAmountDistribution(20000, 20000000)
                ],
            ]
        ];
        // foreach ($dataArr as $k => $v) {
        //     $data[$k]['name'] = $employee_model->getEmployeeValueByKey('uuid', $v['uuid'], 'real_name');
        //     $data[$k]['value'] = $v['count(uuid)'];
        // }
        // return $goods_model->getOrederAmountDistribution(0, 20);
        return $this->create($dataArr, '查询成功');
    }

    //获取月度佣金排行榜
    public function expecCommissionRanking()
    {
        $per_model = new PerformanceModel();
        $employee_model = new EmployeeModel();
        $goods_model = new GoodsModel();

        // $sum_commission = [];

        $all_uuid = $per_model->getPerformanceAllUuid();
        foreach ($all_uuid as $k => $v) {
            $sum_commission = 0;

            $data[$k]['name'] = $employee_model->getEmployeeValueByKey('uuid', $v['uuid'], 'real_name');
            // $data[$k]['value'] = $v['count(uuid)'];
            $goods_id_list = $per_model->getGoodsIdByUuid($v['uuid']);
            foreach ($goods_id_list as $k1 => $v1) {
                // $test[$k1] = $v1;
                $sum_commission += $goods_model->getCountCommissionByGoodsId($v1);
            }
            // $data[$k]['goods_id_count'] = count($goods_id_list);
            $data[$k]['sum_commission'] = $sum_commission / 100;

            // break;
        }
        //知识点: 对二维关联数组进行排序
        $sum_commission = array_column($data, 'sum_commission');
        array_multisort($sum_commission, SORT_DESC, $data);
        return $this->create($data);
    }


    //获取每日出单量（订单）
    public function getOrderSumByDayInFourWeek()
    {
        $goods_model = new GoodsModel();
        $res = $goods_model->countOrderSumByDayInFourWeek();
        return $this->create($res, '查询成功');
    }

    //获取每日出单量（订单）
    public function getPerformanceAndCommissionSumInMonth()
    {
        $goods_model = new GoodsModel();
        $res = $goods_model->countPerformanceAndCommissionSumInMonth();
        return $this->create($res, '查询成功');
    }


    //获取每日出单量（订单）
    public function getGoodsNumSumInMonth()
    {
        $goods_model = new GoodsModel();
        $res = $goods_model->countGoodsNumInMonth();
        return $this->create($res, '查询成功');
    }




    //结束
}
