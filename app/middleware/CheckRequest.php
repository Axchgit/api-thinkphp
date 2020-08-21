<?php
/*
 * @Author: xch
 * @Date: 2020-08-19 14:18:43
 * @LastEditTime: 2020-08-21 15:06:32
 * @LastEditors: xch
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\middleware\CheckRequest.php
 * @Description: 
 */

declare(strict_types=1);

namespace app\middleware;

use app\controller\Base;

// use think\Request;


class CheckRequest extends Base
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next, int $need_role = 1)
    {
        //
        $token = request()->header('Authorization');
        if (empty($token)) {
            return $this->create('', '令牌不存在', 304);
        }
        $res = checkToken($token);
        if ($res['code'] == 2) {
            return $this->create('', $res['msg'], 304);
        }
        if ($res['data']->role >= $need_role) {
            return $this->create('', '没有权限', 204);
        };
        // return $this->create('', '没有权限', 204);

        $request->data = $res;
        $response = $next($request);
        return $response;
    }
}
