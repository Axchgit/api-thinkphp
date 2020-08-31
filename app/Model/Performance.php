<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-08-31 16:32:51
 * @LastEditors: xch
 * @Description: 员工信息
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\Model\Performance.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;


class Performance extends Model
{

    public function insertPerformance($data){
        return $this->allowField(['uuid','goods_id'])->save($data);
    }

}
