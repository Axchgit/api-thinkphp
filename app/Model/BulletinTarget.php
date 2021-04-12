<?php

/*
 * @Description: 
 * @Author: xch
 * @Date: 2020-11-23 01:30:28
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\Model\BulletinTarget.php
 * @LastEditTime: 2021-04-12 15:59:45
 * @LastEditors: xch
 */
namespace app\model;


use think\Model;
use think\facade\Db;


class BulletinTarget extends Model
{
    //添加记录
    public function createBulletinTarget($data)
    {
        // $bt_model = new BulletinTargetModel();

        try {
            // Transfer::create($data,['number', 'contacts_phone','receive_organization','reason','remarks','review_status','reviewer']);
            $this->create($data, ['bulletin_id', 'target_person']);
            
            return true;
        } catch (\Exception $e) {
            return  $e->getMessage();
        }
    }





    //over
}


