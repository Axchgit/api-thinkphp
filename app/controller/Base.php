<?php
/*
 * @Author: xch
 * @Date: 2020-08-16 16:34:47
 * @LastEditTime: 2020-08-17 16:11:13
 * @LastEditors: xch
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\controller\Base.php
 * @Description: 
 */

namespace app\controller;

use think\Response;

abstract class Base
{
    protected function create($data, $msg = '', $code = 200, $type = 'json')
    {
        //标准Api结构生成
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];

        //返回Api接口
        return Response::create($result,$type);

    }

    protected function tokenCheck($token){
        return $res = checkToken($token);
        if($res['code'] == 1){
            return $res;
        }else{
            return $this->create('','token出错:'.$res['msg'],304);
        }
    }




}
