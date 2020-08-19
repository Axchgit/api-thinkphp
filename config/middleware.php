<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 11:15:58
 * @LastEditTime: 2020-08-19 15:25:36
 * @LastEditors: xch
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\config\middleware.php
 * @Description: 
 */
// 中间件配置
return [
    // 别名或分组
    'alias'    => [
        'checkrequest' => \app\middleware\CheckRequest::class,
    ],
    // 优先级设置，此数组中的中间件会按照数组中的顺序优先执行
    'priority' => [],
];
