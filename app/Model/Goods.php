<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-08-30 17:19:59
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\Model\Goods.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;


class Goods extends Model
{
    public function insertGoods($dataArr)
    {
        $goods = [];
        //CODE:将二维关联数组转换为数据库数组
        foreach ($dataArr as $k => $v) {
            //CODE：去除字符串内某个字符
            // $count = strpos($v['B'], "-");
            // $strlen = strlen('-');
            // $goods[$k]['id'] = substr_replace($v['B'], "", $count, $strlen);
            $goods[$k]['order_id'] = $v['订单号'];
            $goods[$k]['f_order_id'] = $v['父单号'];
            $goods[$k]['order_status'] = $v['订单状态'];
            $goods[$k]['payment_time'] = $v['下单时间'];
            $goods[$k]['goods_id'] = $v['商品ID'];
            $goods[$k]['goods_name'] = $v['商品名称'];
            $goods[$k]['shop_name'] = $v['商品店铺名称'];
            $goods[$k]['goods_number'] = $v['商品数量'];
            $goods[$k]['after_sale_number'] = $v['商品售后中数量'];
            $goods[$k]['return_goods_number'] = $v['商品已退货数量'];
            $goods[$k]['is_same_shop'] = $v['同跨店'];
            $goods[$k]['payment_amount'] = $v['付款金额'] * 100;     //*100 让数据以整数存储
            $goods[$k]['commission_rate'] = $v['佣金比例'] * 1000;
            $goods[$k]['division_proportion'] = $v['分成比例'] * 100;
            $goods[$k]['expec_amount'] = $v['预估金额'] * 100;
            $goods[$k]['expec_commission'] = $v['预估佣金'] * 100;
            $goods[$k]['actual_amount'] = $v['实际金额'] * 100;
            $goods[$k]['actual_commission'] = $v['实际佣金'] * 100;
            $goods[$k]['settlement_information'] = empty($v['结算信息']) ? '' : $v['结算信息'];
            $goods[$k]['order_platform'] = $v['下单平台'];
            $goods[$k]['is_plus'] = $v['plus订单'];
            $goods[$k]['promotion_position_id'] = $v['推广位ID'];
            $goods[$k]['promotion_position_name'] = empty($v['推广位名称']) ? '' : $v['推广位名称'];
            $goods[$k]['PID'] = empty($v['PID']) ? '' : $v['PID'];
            $goods[$k]['third_party_position'] = empty($v['第三方服务平台']) ? '' : $v['第三方服务平台'];
            $goods[$k]['website_id'] = $v['网站ID'];
            $goods[$k]['promotion_role'] = $v['推广角色'];
            $goods[$k]['group_activity_id'] = $v['团活动ID'];
            $goods[$k]['group_activity_name'] = empty($v['团活动名称']) ? '' : $v['团活动名称'];


            //礼金批次	礼金分摊金额	是否京喜红包	是否拼购	是否复购	是否首购	是否联盟礼金	是否推客礼金
        }
        if (empty($goods)) {
            return false;
        } else {
            return $this->saveAll($goods);
        }
    }
}
