<?php
/*
 * @Author: xch
 * @Date: 2020-08-17 22:03:01
 * @LastEditTime: 2020-08-30 16:55:46
 * @LastEditors: xch
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\controller\Goods.php
 * @Description: 
 */

declare(strict_types=1);

namespace app\controller;

use think\Request;

use app\model\Goods as GoodsModel;
// use app\model\EmployeeLogin as EmpLoginModel;

use think\facade\Db;

class Goods extends Base
{
    public function uploadExcel()
    {
        // return $this->create('popop','sfsdfsfdsf',200);
        $post =  request()->param();

        $goods_model = new GoodsModel();
        // return $this->create($post,'sfsdfsfdsf',200);


        $data = $goods_model->insertGoods($post);

        if ($data) {
            return $this->create('', '成功', 200);
        } else {
            return $this->create('', '失败', 204);
        }
    }
}
