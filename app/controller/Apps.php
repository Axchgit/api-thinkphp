<?php
/*
 * @Description: 
 * @Author: xch
 * @Date: 2021-10-27 12:10:58
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\controller\Apps.php
 * @LastEditTime: 2021-10-27 12:13:12
 * @LastEditors: xch
 */
declare(strict_types=1);

namespace app\controller;

// use think\facade\Request;
use think\Request;
use app\model\Admin as AdminModel;
use think\facade\Db;
use app\model\Goods as GoodsModel;
use app\model\GoodsTemp as GoodsTempModel;
use app\model\Employee as EmployeeModel;

class Apps extends Base{
    
    public function callback(){

        // $goods_model = new GoodsModel();
        // return '132';
        // return $goods_model->incrementalUpdata();
        return $this->create('', '成功', 200);


    }
}
