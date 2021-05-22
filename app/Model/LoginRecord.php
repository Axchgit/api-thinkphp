<?php
/*
 * @Author: 罗曼
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2021-05-22 21:07:28
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\Model\LoginRecord.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

use think\Model;

use think\facade\Db;



class LoginRecord extends Model
{

    //根据学工号查询登录记录
    public function selectRecord($uuid, int $count = 10)
    {
        return $this->where('uuid', $uuid)->order('id', 'desc')->limit($count)->select();
    }

    //获取员工登录记录
    public function countRecordByDay()
    {
        // 获取今天06点时间戳
        $nowStamp = mktime(6, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y'));


        $subsql =  Db::table('employee')
            ->alias('a')
            ->leftjoin('login_record b', 'a.uuid = b.uuid')
            ->field('a.uuid,max(b.id) as max_id')
            ->group('real_name')
            ->buildSql();

        $list =  Db::table('employee')
            ->alias('a')
            ->join([$subsql => 'c'], 'a.uuid = c.uuid')
            ->leftjoin('login_record b', 'c.max_id = b.id')
            ->field('a.uuid,b.create_time')
            ->select();


        foreach ($list as $k => $v) {
            $start_time = date('Y-m-d H:i:s',$nowStamp);
            $end_time = date('Y-m-d H:i:s', strtotime('+1 days', $nowStamp));
            $count[$k][0] = Db::table('employee')->where('uuid', $v['uuid'])->value('real_name');
            $count[$k][1] = $this->where('uuid', $v['uuid'])->whereBetweenTime('create_time', $start_time, $end_time)->group('uuid')->count();
            if($v['create_time'] != null){
                $count[$k][2] = date('y/m/d H:i',strtotime($v['create_time']));

            }else{
                $count[$k][2] = '从未登录';
            }
        }




        return $count;


        // ->join([$subsql => 'c'], 'a.number = c.number')
        // ->join('join_apply d', 'd.id = c.id')
    }
}
