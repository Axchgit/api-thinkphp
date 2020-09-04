<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-09-04 19:03:50
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\Model\GoodsTemp.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;


class GoodsTemp extends Model
{
    //插入报表
    public function insertGoods($dataArr)
    {
        $goods = [];

        foreach ($dataArr as $k => $v) {

                $goods[$k]['platform'] = empty($k['推手duoid']) ? 'jd' : 'pdd';

                //CODE：去除字符串内某个字符
                $count = strpos($v['订单号'], "-");
                $strlen = strlen('-');
                $goods[$k]['order_id'] = substr_replace($v['订单号'], "", $count, $strlen);
                $goods[$k]['order_status'] = $v['订单状态']; //
                $goods[$k]['payment_time'] = empty($v['支付时间']) ? $v['下单时间'] : $v['支付时间']; //
                $goods[$k]['goods_id'] = empty($v['商品id']) ? $v['商品ID'] : $v['商品id'];
                $goods[$k]['goods_name'] = $v['商品名称'];
                $goods[$k]['shop_name'] = empty($v['店铺id']) ? $v['商品店铺名称'] : $v['店铺id'];
                $goods[$k]['payment_amount'] = empty($v['订单金额(元)']) ? $v['付款金额'] * 100 : $v['订单金额(元)'] * 100;     //*100 让数据以整数存储
                $goods[$k]['commission_rate'] = empty($v['招商佣金']) ?  $v['佣金比例'] * 1000 : substr($v['招商佣金'], 0, -1)*10;
                $goods[$k]['expec_commission'] = empty($v['招商收入(元)']) ? $v['预估佣金'] * 100 : $v['招商收入(元)'] * 100;

                $goods[$k]['leader_nickname'] = empty($v['招商团长昵称']) ? '' : $v['招商团长昵称'];
                $goods[$k]['leader_duoid'] = empty($v['招商duoid']) ? '' : $v['招商duoid'];
                $goods[$k]['salesman_nickname'] = empty($v['推手昵称']) ? '' : $v['推手昵称'];
                $goods[$k]['salesman_duoid'] = empty($v['推手duoid']) ? '' : $v['推手duoid'];
                $goods[$k]['salesman_commission'] = empty($v['推手佣金']) ? '' : substr($v['推手佣金'], 0, -1)*10;


                $goods[$k]['after_sale_number'] = empty($v['商品售后中数量']) ? '' : $v['商品售后中数量'];
                $goods[$k]['return_goods_number'] = empty($v['商品已退货数量']) ? '' : $v['商品已退货数量'];
                $goods[$k]['is_same_shop'] = empty($v['同跨店']) ? '' : $v['同跨店'];
                $goods[$k]['division_proportion'] = empty($v['分成比例']) ? '' : $v['分成比例'] * 100;
                $goods[$k]['expec_amount'] = empty($v['预估金额']) ? '' : $v['预估金额'] * 100;
                $goods[$k]['actual_amount'] = empty($v['实际金额']) ? '' : $v['实际金额'] * 100;
                $goods[$k]['actual_commission'] = empty($v['实际佣金']) ? '' : $v['实际佣金'] * 100;
                $goods[$k]['goods_number'] = empty($v['商品数量']) ? '' : $v['商品数量'];
                $goods[$k]['f_order_id'] = empty($v['父单号']) ? '' : $v['父单号'];
                $goods[$k]['settlement_information'] = empty($v['结算信息']) ? '' : $v['结算信息'];
                $goods[$k]['order_platform'] = empty($v['下单平台']) ? '' : $v['下单平台'];
                $goods[$k]['is_plus'] = empty($v['plus订单']) ? '' : $v['plus订单'];
                $goods[$k]['promotion_position_id'] = empty($v['推广位ID']) ? '' : $v['推广位ID'];
                $goods[$k]['promotion_position_name'] = empty($v['推广位名称']) ? '' : $v['推广位名称'];
                $goods[$k]['PID'] = empty($v['PID']) ? '' : $v['PID'];
                $goods[$k]['third_party_position'] = empty($v['第三方服务平台']) ? '' : $v['第三方服务平台'];
                $goods[$k]['website_id'] = empty($v['网站ID']) ? '' : $v['网站ID'];
                $goods[$k]['promotion_role'] = empty($v['推广角色']) ? '' : $v['推广角色'];
                $goods[$k]['group_activity_id'] = empty($v['团活动ID']) ? '' : $v['团活动ID'];
                $goods[$k]['group_activity_name'] = empty($v['团活动名称']) ? '' : $v['团活动名称'];

            }
        // }

        if (empty($goods)) {
            return false;
        } else {
            return $this->saveAll($goods);
        }
    }

       //CODE:增量更新数据
       public function incrementalUpdata()
       {
           /*更新数据*/
           //查询重复数据
           $same = Db::view(['goods' => 'a'], 'id,order_id', 'a.order_id= b.order_id')
               ->view(['goods_temp' => 'b'])
               // ->where('a.order_number'=='b.order_number')
               ->select()->toArray();
            //    return $same;
           //更新语句
           return $this->saveAll($same);
   
           //删除临时表里的重复数据
           foreach ($same as $k => $v) {
               Db::table('goods_temp')->where('id', $v['id'])->delete();
           }
   
           /*插入新增数据*/
   
           //查询剩余数据
           $data = Db::table('goods_temp')->select()->toArray();
           if (empty($data)) {
               //返回数据优化
               return '无新增';
           }
   
           //删除临时表里的剩余数据
           //FIXME:
           $this->delete(true);
   
           // foreach ($data as $k => $v) {
           //     Db::table('goods_temp')->where('id', $v['id'])->delete();
           // }
   
           //插入新增数据到goods
           $this->insertAll($data);
           //TODO:返回数据优化
           return '成功';
       }
   


}
