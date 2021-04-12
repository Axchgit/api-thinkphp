<?php

/*
 * @Description: 
 * @Author: xch
 * @Date: 2020-11-23 01:31:32
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\Model\BulletinRead.php
 * @LastEditTime: 2021-04-13 00:26:22
 * @LastEditors: xch
 */
namespace app\model;

// use PHPExcel_IOFactory;
// use think\Db;
use think\Model;
use think\facade\Db;


class BulletinRead extends Model
{
    //添加记录
    public function createBulletinRead($data)
    {
        // $bt_model = new BulletinTargetModel();
        $count = $this->where('bulletin_id',$data['bulletin_id'])->where('target_uid',$data['target_uid'])->count();
        if($count !== 0){
            return true;
        }
        try {
            // Transfer::create($data,['number', 'contacts_phone','receive_organization','reason','remarks','review_status','reviewer']);
            $this->create($data, ['bulletin_id', 'target_uid']);
            return true;
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }





    //over
}


