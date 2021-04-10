<?php
/*
 * @Description: 验证码模型类
 * @Author: xch
 * @Date: 2021-04-10 23:40:07
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\Model\TempCode.php
 * @LastEditTime: 2021-04-11 00:03:31
 * @LastEditors: xch
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;


class TempCode extends Model
{
    //保存验证码
    public function saveCode($uuid, $code, $msg = '验证码')
    {
        try {
            $this->save(['uuid' => $uuid, 'code' => $code, 'msg' => $msg]);
            // $this->where('uuid', $uuid)->delete();
            return true;
        } catch (\Exception  $e) {
            return $e->getMessage();
        }
    }
    //获取验证码
    public function getCode($uuid)
    {
        try {
            return $this->where('uuid', $uuid)->value('code');
        } catch (\Exception  $e) {
            return false;
        }
    }
    //根据学号获取验证码
    public function getCodeInfoByUuid($uuid)
    {
        try {
            return $this->where('uuid', $uuid)->find();
        } catch (\Exception  $e) {
            return false;
        }
    }
    //删除验证码
    public function deleteCode($uuid)
    {
        try {
            $this->where('uuid', $uuid)->delete();
            return true;
        } catch (\Exception  $e) {
            return $e->getMessage();
        }
    }
}
