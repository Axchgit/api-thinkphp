<?php
/*
 * @Author: 罗曼
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2021-04-09 17:20:44
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\Model\LoginRecord.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

use think\Model;


class LoginRecord extends Model
{
    //根据学工号查询登录记录
    public function selectRecord($uuid,int $count = 10){
        return $this->where('uuid',$uuid)->order('id', 'desc')->limit($count)->select();        
    }

}
