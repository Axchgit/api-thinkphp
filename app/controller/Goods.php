<?php
/*
 * @Author: xch
 * @Date: 2020-08-17 22:03:01
 * @LastEditTime: 2020-09-15 14:00:07
 * @LastEditors: Chenhao Xing
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\controller\Goods.php
 * @Description: 
 */

declare(strict_types=1);

namespace app\controller;

use think\Request;

use app\model\Goods as GoodsModel;
// use app\model\EmployeeLogin as EmpLoginModel;

use think\facade\Db;
use think\facade\Cache;

class Goods extends Base
{
    /**
     * @description: 上传报表,读取报表数据存入数据库
     * @param {type} 
     * @return {type} 
     */
    public function uploadExcel()
    {
        $post =  request()->param();
        $goods_model = new GoodsModel();
        $res = $goods_model->insertGoods($post);
        if ($res === true) {
            return $this->create('', '添加成功', 200);
        } else {
            return $this->create('', $res, 204);
        }
    }
    //获取进度
    public function getSpeed(){
        // $goods_model = new GoodsModel();
        // $goods_model->speed();
        $data = [
            'speed1'=>Cache::get('speed1', '未缓存'),
            'speed2'=>Cache::get('speed2', '未缓存'),
            'speed3'=>Cache::get('speed3', '未缓存'),
            'speed4'=>Cache::get('speed4', '未缓存'),
            'speed5'=>Cache::get('speed5', '未缓存'),
            'speed6'=>Cache::get('speed6', '未缓存'),

        ];
        return $this->create($data, '添加成功', 200);

        // return Cache::get('speed', '未缓存');
    }
    /**
     * @description: goods查询
     * @param string key:字段名
     * @param string value:字段值
     * @param int list_rows:每页数据条数
     * @param array query:分页查询条件-->page(页码)
     * @return  Api
     */
    public function selectGoods()
    {
        $post =  request()->param();
        $goods_model = new GoodsModel();
        $key = !empty($post['key']) ? $post['key'] : '';
        $value = !empty($post['value']) ? $post['value'] : '';
        $data = $goods_model->selectGoods($key, $value, $post['list_rows'], false, ['query' => $post]);
        if ($data) {
            return $this->create($data, '查询成功');
        } else {
            return $this->create($data, '暂无数据', 204);
        }
    }



    //结束
}
