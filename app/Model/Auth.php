<?php
/*
 * @Description: 
 * @Author: xch
 * @Date: 2021-01-02 13:38:30
 * @FilePath: \testd:\wamp64\www\api-thinkphp\app\Model\Auth.php
 * @LastEditTime: 2021-01-07 16:44:32
 * @LastEditors: xch
 */
namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;


class Auth extends Model
{

    public function createAuth($qruid, $ip,$address)
    {
        try {
            $this->save(['qruid' => $qruid, 'auth_ip' => $ip,'auth_address'=>$address]);
            return true;
        } catch (\Exception  $e) {
            return $e->getMessage();
        }
    }
    //查找auth信息
    public function findAuth($qruid)
    {
        return $this->where('qruid', $qruid)->find();
    }

    //更新状态
    public function updateAuth($qruid,$auth_state,$user_uuid){
        try {
            $this->update(['auth_state' => $auth_state,'work_num'=>$user_uuid], ['qruid' => $qruid]);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    //over
}
