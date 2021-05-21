<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2021-05-22 02:44:44
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\Model\Goods.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;
use think\facade\Cache;
use app\model\GoodsTemp as GoodsTempModel;


class Goods extends Model
{
    //插入报表
    public function insertGoods($dataArr)
    {
        ini_set("memory_limit", "-1");


        // Cache::set('speed1', time());

        // $gt_mode = new GoodsTempModel();
        $goods = [];
        //CODE:将二维关联数组转换为数据库数组
        // foreach ($dataArr[0] as $k => $v) {
        //     $isJd = empty($k['推手duoid']) ? true : false;
        // }
        foreach ($dataArr as $k => $v) {
            // if ($isJd) {
            //     $goods[$k]['order_id'] = $v['订单号'];
            //     $goods[$k]['f_order_id'] = $v['父单号'];
            //     $goods[$k]['order_status'] = $v['订单状态'];
            //     $goods[$k]['payment_time'] = $v['下单时间'];
            //     $goods[$k]['goods_id'] = $v['商品ID'];
            //     $goods[$k]['goods_name'] = $v['商品名称'];
            //     $goods[$k]['shop_name'] = $v['商品店铺名称'];
            //     $goods[$k]['goods_number'] = $v['商品数量'];
            //     $goods[$k]['after_sale_number'] = $v['商品售后中数量'];
            //     $goods[$k]['return_goods_number'] = $v['商品已退货数量'];
            //     $goods[$k]['is_same_shop'] = $v['同跨店'];
            //     $goods[$k]['payment_amount'] = $v['付款金额'] * 100;     //*100 让数据以整数存储
            //     $goods[$k]['commission_rate'] = $v['佣金比例'] * 1000;
            //     $goods[$k]['division_proportion'] = $v['分成比例'] * 100;
            //     $goods[$k]['expec_amount'] = $v['预估金额'] * 100;
            //     $goods[$k]['expec_commission'] = $v['预估佣金'] * 100;
            //     $goods[$k]['actual_amount'] = $v['实际金额'] * 100;
            //     $goods[$k]['actual_commission'] = $v['实际佣金'] * 100;
            //     $goods[$k]['settlement_information'] = empty($v['结算信息']) ? '' : $v['结算信息'];
            //     $goods[$k]['order_platform'] = $v['下单平台'];
            //     $goods[$k]['is_plus'] = $v['plus订单'];
            //     $goods[$k]['promotion_position_id'] = $v['推广位ID'];
            //     $goods[$k]['promotion_position_name'] = empty($v['推广位名称']) ? '' : $v['推广位名称'];
            //     $goods[$k]['PID'] = empty($v['PID']) ? '' : $v['PID'];
            //     $goods[$k]['third_party_position'] = empty($v['第三方服务平台']) ? '' : $v['第三方服务平台'];
            //     $goods[$k]['website_id'] = $v['网站ID'];
            //     $goods[$k]['promotion_role'] = $v['推广角色'];
            //     $goods[$k]['group_activity_id'] = $v['团活动ID'];
            //     $goods[$k]['group_activity_name'] = empty($v['团活动名称']) ? '' : $v['团活动名称'];
            //     $goods[$k]['platform'] = empty($k['推手duoid']) ? 'jd' : 'pdd';
            //     //礼金批次	礼金分摊金额	是否京喜红包	是否拼购	是否复购	是否首购	是否联盟礼金	是否推客礼金
            // } else {

            // [ 
            //    "支付时间": "2020-09-02 08:25:03",payment_time
            // "订单号": "200902-513467352281554",
            // "商品名称": "巴布豆旗舰店1-6年级儿童书包小学生6到12周岁护脊减负男女童书包",goods_name
            // "商品id": "14771538174",goods_id
            // "店铺id": "3305480",shop_name
            // "招商团长昵称": "未设置昵称",leader_nickname
            // "招商duoid": "2021603",leader_duoid
            // "推手昵称": "大拼客官网2",salesman_nickname
            // "推手duoid": "20002",salesman_duoid
            // "订单状态": "已成团",order_status
            // "订单金额(元)": "47.84",  $v['付款金额']payment_amount
            // "推手佣金": "4%",salesman_commission,
            // "招商佣金": "3%", commission_rate
            // "招商收入(元)": "1.44"]; expec_commission,

            $goods[$k]['platform'] = empty($v['推手duoid']) ? 'jd' : 'pdd';

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
            $goods[$k]['commission_rate'] = empty($v['招商佣金']) ?  $v['佣金比例'] * 1000 : substr($v['招商佣金'], 0, -1) * 10;
            // $goods[$k]['expec_commission'] = empty($v['招商收入(元)']) ? $v['预估佣金'] * 100 : $v['招商收入(元)'] * 100;
            $goods[$k]['expec_commission'] = empty($v['招商收入(元)']) ? (empty($v['预估佣金']) ? 0 : $v['预估佣金']) : $v['招商收入(元)'] * 100;
            // 招商收入(元)
            $goods[$k]['leader_nickname'] = empty($v['招商团长昵称']) ? '' : $v['招商团长昵称'];
            $goods[$k]['leader_duoid'] = empty($v['招商duoid']) ? '' : $v['招商duoid'];
            $goods[$k]['salesman_nickname'] = empty($v['推手昵称']) ? '' : $v['推手昵称'];
            $goods[$k]['salesman_duoid'] = empty($v['推手duoid']) ? '' : $v['推手duoid'];
            $goods[$k]['salesman_commission'] = empty($v['推手佣金']) ? '' : substr($v['推手佣金'], 0, -1) * 10;
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
            if ($goods[$k]['expec_commission'] == 0) {
                unset($goods[$k]);
                //使用array_values()重新建立索引
                $goods = array_values($goods);
            }
        }
        // Cache::set('speed2', time());
        // }
        Db::startTrans();
        try {
            if (!empty($goods)) {
                // $gt_mode->limit(100)->insertAll($goods);
                Db::table('goods_temp')->limit(100)->insertAll($goods);
                // Db::table('goods_temp')->saveAll($goods);
                // $gt_mode = new GoodsTempModel();
                // $gt_mode->saveUpload($goods);
            } else {
                // Db::rollback();
                return false;
            }
            // Cache::set('speed3', time());
            //查询重复数据
            $same = Db::view('goods')
                ->view('goods_temp', 'goods_name', 'goods.order_id = goods_temp.order_id')
                ->select();
            //删除表里的重复数据
            foreach ($same as $k => $v) {
                Db::table('goods')->where('order_id', $v['order_id'])->delete();
            }
            // Cache::set('speed4', time());
            //查询临时表数据
            //知识点:查询时忽略某个字段
            $data = Db::table('goods_temp')->withoutField('id')->select()->toArray();
            if (empty($data)) {
                // Db::rollback();
                return '临时表数据为空';
            }
            $res = $this->limit(100)->insertAll($data);
            // Cache::set('speed5', time());
            if ($res) {
                Db::table('goods_temp')->delete(true);
                Db::commit();
                // Cache::set('speed6', time());
                return true;
            } else {
                // Db::rollback();
                return '插入goods表失败' . $res;
            }
        } catch (\Exception  $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }


    //查询goods
    public function selectGoods($key, $value, $list_rows = 10, $isSimple = false, $config = '')
    {
        switch ($key) {
            case 'order_id':
                $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                break;
            case 'goods_id':
                $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                break;
            case 'goods_name':
                $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                break;
            case 'shop_name':
                $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                break;
            default:
                $data = $this->paginate($list_rows, $isSimple, $config);
        }
        if (empty($data)) {
            return false;
        } else {
            return $data;
        }
    }


    /**************DataView方法 */
    //获取订单金额分布
    public function getOrederAmountDistribution($start, $end)
    {
        return $this->whereBetween('payment_amount', $start . ',' . $end)->count();
    }

    public function getCountCommissionByGoodsId($goods_id)
    {
        try {
            $sum = $this->where('goods_id', $goods_id)->sum('expec_commission');
            return $sum;
        } catch (\Exception  $e) {
            return 0;
        }
    }


    //统计近四周出单量
    public function countOrderSumByDayInFourWeek()
    {

        // $data = $this->whereBetweenTime('create_time', $startDay, $endDay)->select();
        // $data = $this->whereBetweenTime('create_time', $startDay,$endDay)->count();

        // Db::name('orders')
        //             ->field('createTime,orderId')
        //             ->whereBetweenTime('createTime', $startDay, $endDay)
        //             ->where('shopId', $shopId)
        //             ->where('orderStatus', '>', 2)
        //             ->select();
        $id = $this->min('id');
        $data = $this->where('id', $id)->value('payment_time');
        $nowStamp = strtotime($data);
        $count = [];
        $start=[];
        $end=[];
        // $startDay = date('Y-m-d H:m:s', strtotime('-5 days', strtotime($data)));
        for ($i = 0; $i < 28; $i++) {
            $j=$i+1;
            // $startTime = date('Y-m-d H:m:s', strtotime('+1 days', $nowStamp));
            // $endTime = date('Y-m-d H:m:s', strtotime('-10 days', $nowStamp));
            $start_time = date('Y-m-d H:m:s', strtotime('-' . "$i" . ' days', $nowStamp));
            $end_time = date('Y-m-d H:m:s', strtotime('-' . "$j" . ' days', $nowStamp));
            $count[$i] = $this->whereBetweenTime('payment_time', $end_time, $start_time)->count();
            // $start[$i] = $start_time;
            // $end[$i] = $end_time;

            //     // $startTime = date('Y-m-d H:m:s',strtotime('-' . "$i" . ' days', $nowStamp));
            //     // $endTime = date('Y-m-d H:m:s',strtotime('-' . "$i+1" . ' days', $nowStamp));
            //     $start_time = $nowStamp - ($i-1 * 24 * 3600);
            //     $payment_time_start =  $start_time / 24 / 3600;
            //     $end_time = $nowStamp - (($i) * 24 * 3600);
            //     $payment_time_end =  $end_time / 24 / 3600;
        }
        // $sum = $this->whereBetween('payment_time',"44000,44071")->count();
        // createTime = (time - 25569) * 24 * 3600
        // $time = strtotime($data);
        return [$nowStamp,$count];
    }
    //统计本月佣金及签约量
    public function countPerformanceAndCommissionSumInMonth(){
        $id = $this->min('id');
        $data = $this->where('id', $id)->value('payment_time');
        $nowStamp = strtotime($data);
        $count = [];
        $countNum=0;
        $start=[];
        $end=[];
        // $startDay = date('Y-m-d H:m:s', strtotime('-5 days', strtotime($data)));
        for ($i = 29; $i >=0; $i--) {
            $j=$i+1;
            
            $start_time = date('Y-m-d H:m:s', strtotime('-' . "$i" . ' days', $nowStamp));
            $end_time = date('Y-m-d H:m:s', strtotime('-' . "$j" . ' days', $nowStamp));
            $count[$countNum] = $this->whereBetweenTime('payment_time', $end_time, $start_time)->sum("expec_commission");
            // $start[$i] = $start_time;
            // $end[$i] = $end_time;
            if($count[$countNum]>=20000){
                $count[$countNum]= 15000;
            }
            $monthDay[$countNum]=date('m/d', strtotime('-' . "$i" . ' days', $nowStamp));
            $countNum+=1;

        }
        return [$monthDay,$count];

    }



        //统计本月佣金及签约量
        public function countGoodsNumInMonth(){
            $id = $this->min('id');
            $data = $this->where('id', $id)->value('payment_time');
            $nowStamp = strtotime($data);
            $count = [];
            $countNum=0;
            $start=[];
            $end=[];
            // $startDay = date('Y-m-d H:m:s', strtotime('-5 days', strtotime($data)));
            for ($i = 29; $i >=0; $i--) {
                $j=$i+1;
                
                $start_time = date('Y-m-d H:m:s', strtotime('-' . "$i" . ' days', $nowStamp));
                $end_time = date('Y-m-d H:m:s', strtotime('-' . "$j" . ' days', $nowStamp));
                $count[$countNum] = $this->whereBetweenTime('payment_time', $end_time, $start_time)->group('goods_id')->count();
                // $start[$i] = $start_time;
                // $end[$i] = $end_time;
                // $monthDay[$countNum]=date('m/d', strtotime('-' . "$i" . ' days', $nowStamp));
                $countNum+=1;
    
            }
            return $count;
    
        }










    //获取进度
    // public function speed()
    // {
    //         // Cache::set('speed', 1);

    //     // for($i=0;$i<10000;$i++){

    //     //     Cache::set('speed', 1);
    //     // }
    //     return Cache::get('speed', '未缓存');
    // }

    /*******废弃代码TODO:删除 */
    // TODO:删除废弃代码
    // public function incrementalUpdata()
    // {

    //     /*更新数据*/
    //     //查询重复数据
    //     $same = Db::view('goods')
    //         ->view('goods_temp', 'goods_name', 'goods.order_id = goods_temp.order_id')
    //         ->select();
    //     // return $same;
    //     //更新语句
    //     // return $this->saveAll($same);

    //     //删除临时表里的重复数据
    //     foreach ($same as $k => $v) {
    //         Db::table('goods')->where('order_id', $v['order_id'])->delete();
    //     }
    //     // return true;
    //     /*插入新增数据*/

    //     //查询剩余数据
    //     $data = Db::table('goods_temp')->withoutField('id')->select()->toArray();
    //     // return json($data);
    //     if (empty($data)) {
    //         //返回数据优化
    //         return '无新增';
    //     }

    //     //删除临时表里的剩余数据
    //     // $this->delete(true);

    //     // foreach ($data as $k => $v) {
    //     //     Db::table('goods_temp')->where('id', $v['id'])->delete();
    //     // }

    //     //插入新增数据到goods
    //     $this->insertAll($data);
    //     return '成功';
    // }

}
