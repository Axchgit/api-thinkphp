<?php

use PHPMailer\PHPMailer\PHPMailer;
use Firebase\JWT\JWT;

// 应用公共文件

/**
 * 发送邮件方法
 * @param $to：接收者 $title：标题 $content：邮件内容
 * @return bool true:发送成功 false:发送失败
 */
function sendMail($to, $title, $content)
{
    //引入PHPMailer的核心文件 使用require_once包含避免出现PHPMailer类重复定义的警告
    // require_once("phpmailer/class.phpmailer.php"); 
    // require_once("phpmailer/class.smtp.php");
    //实例化PHPMailer核心类
    $mail = new PHPMailer();
    //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
    // $mail->SMTPDebug = 1;
    //使用smtp鉴权方式发送邮件
    $mail->isSMTP();
    //smtp需要鉴权 这个必须是true
    $mail->SMTPAuth = config('email.smtp_auth');
    //链接qq域名邮箱的服务器地址
    $mail->Host = config('email.host');
    //设置使用ssl加密方式登录鉴权
    $mail->SMTPSecure = 'ssl';
    //设置ssl连接smtp服务器的远程服务器端口号，以前的默认是25，但是现在新的好像已经不可用了 可选465或587
    $mail->Port = config('email.port');
    //设置smtp的helo消息头 这个可有可无 内容任意
    // $mail->Helo = 'Hello smtp.qq.com Server';
    //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
    $mail->Hostname = config('email.host_name');
    //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
    $mail->CharSet = config('email.charset');
    //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName = config('email.from_name');
    //smtp登录的账号 这里填入字符串格式的qq号即可
    $mail->Username = config('email.user_name');
    //smtp登录的密码 使用生成的授权码（就刚才叫你保存的最新的授权码）
    $mail->Password = config('email.password');
    //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
    $mail->From = config('email.from');
    //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
    $mail->isHTML(config('email.isHTML'));
    //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
    $mail->addAddress($to, '');
    //添加多个收件人 则多次调用方法即可
    // $mail->addAddress('xxx@163.com','lsgo在线通知');
    //添加该邮件的主题
    $mail->Subject = $title;
    //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
    $mail->Body = $content;
    //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
    // $mail->addAttachment('./d.jpg','mm.jpg');
    //同样该方法可以多次调用 上传多个附件
    // $mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');
    $status = $mail->send();
    //简单的判断与提示信息
    if ($status) {
        return true;
    } else {
        return false;
    }
}

/**
 * @description: 创建全球唯一标识符
//  * @param {type} 
 * @return array|string
 */
function  createGuid()
{
    if (function_exists('com_create_guid')) {
        return com_create_guid();
    } else {
        $guid = '';
        $namespace = rand(11111, 99999);
        $uid = uniqid('', true);
        $data = $namespace;
        $data .= $_SERVER['REQUEST_TIME'];
        $data .= $_SERVER['HTTP_USER_AGENT'];
        $data .= $_SERVER['REMOTE_ADDR'];
        $data .= $_SERVER['REMOTE_PORT'];
        $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
        $guid = substr($hash,  0,  8) . '-' .
            substr($hash,  8,  4) . '-' .
            substr($hash, 12,  4) . '-' .
            substr($hash, 16,  4) . '-' .
            substr($hash, 20, 12);
        return $guid;
    }
}
/**
 * @description: 生成token
 * @param {type} undefined
 * @param mixed $uuid
 * @return string $jwt:生成的jwt字符串
 */
function signToken($uuid, $role)
{
    $key = config('login.token_key');         //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
    $token = array(
        "iss" => $key,        //签发者 可以为空
        "aud" => '',          //面象的用户，可以为空
        "iat" => time(),      //签发时间
        "nbf" => time(),    //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
        "exp" => time() + 2592000, //token 过期时间
        "data" => [           //记录的userid的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
            'uuid' => $uuid,
            'role' => $role,
        ]
    );
    //  print_r($token);
    $jwt = JWT::encode($token, $key, "HS256");  //根据参数生成了 token
    return $jwt;
}
/**
 * @description: 验证token
 * @param mixed $token:需要验证的token
 * @return array|voide 返回成功结果或者失败信息
 */
function checkToken($token)
{
    $key = config('login.token_key');
    $status = array("code" => 2);
    try {
        JWT::$leeway = 60; //当前时间减去60，把时间留点余地
        $decoded = JWT::decode($token, $key, array('HS256')); //HS256方式，这里要和签发的时候对应
        $arr = (array)$decoded;
        $res['code'] = 1;
        $res['msg'] = "成功";
        $res['data'] = $arr['data'];
        return $res;
    } catch (\Firebase\JWT\SignatureInvalidException $e) { //签名不正确
        $status['msg'] = "签名不正确";
        return $status;
    } catch (\Firebase\JWT\BeforeValidException $e) { // 签名在某个时间点之后才能用
        $status['msg'] = "token失效";
        return $status;
    } catch (\Firebase\JWT\ExpiredException $e) { // token过期
        $status['msg'] = "token失效";
        return $status;
    } catch (Exception $e) { //其他错误
        $status['msg'] = "未知错误";
        return $status;
    }
}
// 知识点:去掉二维数组中的重复数据
function remove_duplicate($array)
{
    $result = array();
    for ($i = 0; $i < count($array); $i++) {
        $source = $array[$i];
        if (array_search($source, $array) == $i && $source <> "") {
            $result[] = $source;
        }
    }
    return $result;
}

//获取真实IP
// function getClientRealIP()  
//获取用户真实IP 
function getClientRealIP()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else 
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else 
            if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else 
                if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return ($ip);
}
//访问url链接
function httpUtil($url, string $method = 'GET')
{
    // $num=input('m');                                     //获取前台提交的手机号
    // $host='http://showphone.market.alicloudapi.com';       //查询主机链接
    // $path="/6-1";
    // $querys="num=".$num;                                 //查询参数
    // $url=$host.$path.'?'.$querys;                           //完整请求链接

    $appcode = '';                                       //阿里云提供的接口app码
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode); //请求头

    // $method='GET';                                               //请求方式

    $curl = curl_init();                                           //初始化一个curl句柄,用于获取其它网站内容
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); //请求方式
    curl_setopt($curl, CURLOPT_URL, $url);   //请求url
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); //请求头
    curl_setopt($curl, CURLOPT_FAILONERROR, false);  //是否显示HTTP状态码
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //执行成功返回结果
    curl_setopt($curl, CURLOPT_HEADER, false);    //是否返回请求头信息
    // if (1 == strpos("$".$host, "https://"))
    // {
    //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//禁止curl验证对等证书
    //     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);//不检查证书
    // }
    $res = curl_exec($curl); //执行查询句柄
    curl_close($curl);    //关闭查询连接
    $resu = json_decode($res, true); //将json数据解码为php数组
    return $resu;

    if ($resu['showapi_res_body']['ret_code'] == -1) {          //返回错误码，查询失败
        // return $this->error('没有查询结果，请重新输入','Index/index');
        return false;
    } else {
        return $resu;

        // $this->assign('num',$num);           //将查询手机号写入模板
        // $this->assign('res',$resu);          //将查询结果php数组写入模板
        // return $this->fetch('index');
    }
}


/**
 * @description: 
 *@param : undefined

 *@param mixed $name 姓名

 *@param mixed $code  验证码

 *@param mixed $operation 操作

 *@param string $call 称呼 [同志,管理员]

 * @return {*} 邮件模板
 */
function emailHtmlModel($name, $code, $operation, string $call = '管理员')
{
    $email_html_alibaba = '<head>
    <base target="_blank" />
    <style type="text/css">::-webkit-scrollbar{ display: none; }</style>
    <style id="cloudAttachStyle" type="text/css">#divNeteaseBigAttach, #divNeteaseBigAttach_bak{display:none;}</style>
    <style id="blockquoteStyle" type="text/css">blockquote{display:none;}</style>
    <style type="text/css">
        body{font-size:14px;font-family:arial,verdana,sans-serif;line-height:1.666;padding:0;margin:0;overflow:auto;white-space:normal;word-wrap:break-word;min-height:100px}
        td, input, button, select, body{font-family:Helvetica, "Microsoft Yahei", verdana}
        pre {white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word;width:95%}
        th,td{font-family:arial,verdana,sans-serif;line-height:1.666}
        img{ border:0}
        header,footer,section,aside,article,nav,hgroup,figure,figcaption{display:block}
        blockquote{margin-right:0px}
    </style>
  </head>
  <body tabindex="0" role="listitem">
  <table width="700" border="0" align="center" cellspacing="0" style="width:700px;">
    <tbody>
    <tr>
        <td>
            <div style="width:700px;margin:0 auto;border-bottom:1px solid #ccc;margin-bottom:30px;">
                <table border="0" cellpadding="0" cellspacing="0" width="700" height="39" style="font:12px Tahoma, Arial, 宋体;">
                    <tbody><tr><td width="210"></td></tr></tbody>
                </table>
            </div>
            <div style="width:680px;padding:0 10px;margin:0 auto;">
                <div style="line-height:1.5;font-size:14px;margin-bottom:25px;color:#4d4d4d;">
                    <strong style="display:block;margin-bottom:15px;">亲爱的<span style="color: green">' . $name . '</span>' . $call . ',<span style="color:#f60;font-size: 16px;"></span>您好！</strong>
                    <strong style="display:block;margin-bottom:15px;">
                        您正在进行<span style="color: red">' . $operation . '</span>操作，请在验证码输入框中输入：<span style="color:#f60;font-size: 24px">' . $code . '</span>，以完成操作。
                    </strong>
                </div>
                <div style="margin-bottom:30px;">
                    <small style="display:block;margin-bottom:20px;font-size:12px;">
                        <p style="color:#747474;">
                            注意：此操作可能会修改您的密码、登录或绑定手机。如非本人操作，请及时登录并修改密码以保证帐户安全
                            <br>（工作人员不会向你索取此验证码，请勿泄漏！)
                        </p>
                    </small>
                </div>
            </div>
            <div style="width:700px;margin:0 auto;">
                <div style="padding:10px 10px 0;border-top:1px solid #ccc;color:#747474;margin-bottom:20px;line-height:1.3em;font-size:12px;">
                    <p>此为系统邮件，请勿回复<br>
                        请保管好您的邮箱，避免账号被他人盗用
                    </p>
                    <p>学创科技有限公司</p>
                </div>
            </div>
        </td>
    </tr>
    </tbody>
</table>
</body>';
    $email_html_jiasudog = '
   <style type="text/css">
       .qmbox html {
         -webkit-text-size-adjust: none;
         -ms-text-size-adjust: none;
       }
   
       @media only screen and (min-device-width: 750px) {
         .qmbox .table750 {
           width: 750px !important;
         }
       }
   
       @media only screen and (max-device-width: 750px),
       only screen and (max-width: 750px) {
         .qmbox table[class="table750"] {
           width: 100% !important;
         }
   
         .qmbox .mob_b {
           width: 93% !important;
           max-width: 93% !important;
           min-width: 93% !important;
         }
   
         .qmbox .mob_b1 {
           width: 100% !important;
           max-width: 100% !important;
           min-width: 100% !important;
         }
   
         .qmbox .mob_left {
           text-align: left !important;
         }
   
         .qmbox .mob_soc {
           width: 50% !important;
           max-width: 50% !important;
           min-width: 50% !important;
         }
   
         .qmbox .mob_menu {
           width: 50% !important;
           max-width: 50% !important;
           min-width: 50% !important;
           box-shadow: inset -1px -1px 0 0 rgba(255, 255, 255, 0.2);
         }
   
         .qmbox .mob_center {
           text-align: center !important;
         }
   
         .qmbox .top_pad {
           height: 15px !important;
           max-height: 15px !important;
           min-height: 15px !important;
         }
   
         .qmbox .mob_pad {
           width: 15px !important;
           max-width: 15px !important;
           min-width: 15px !important;
         }
   
         .qmbox .mob_div {
           display: block !important;
         }
       }
   
       @media only screen and (max-device-width: 550px),
       only screen and (max-width: 550px) {
         .qmbox .mod_div {
           display: block !important;
         }
       }
   
       .qmbox .table750 {
         width: 750px;
       }
     </style>
     
     
     
     
   
   
   
   
     <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: #f3f3f3; min-width: 350px; font-size: 1px; line-height: normal;">
       <tbody><tr>
         <td align="center" valign="top">
           
           <table cellpadding="0" cellspacing="0" border="0" width="750" class="table750" style="width: 100%; max-width: 750px; min-width: 350px; background: #f3f3f3;">
             <tbody><tr>
               <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
               <td align="center" valign="top" style="background: #ffffff;">
   
                 <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100% !important; min-width: 100%; max-width: 100%; background: #f3f3f3;">
                   <tbody><tr>
                     <td align="right" valign="top">
                       <div class="top_pad" style="height: 25px; line-height: 25px; font-size: 23px;">&nbsp;</div>
                     </td>
                   </tr>
                 </tbody></table>
   
                 <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                   <tbody><tr>
                     <td align="left" valign="top">
                       <div style="height: 39px; line-height: 39px; font-size: 37px;">&nbsp;</div>
                       <font class="mob_title1" face=""Source Sans Pro", sans-serif" color="#1a1a1a" style="font-size: 52px; line-height: 55px; font-weight: 300; letter-spacing: -1.5px;">
                         <!-- <a href="https://www.jiasu.dog" style="text-decoration:none" rel="noopener" target="_blank"> -->
                           <span class="mob_title1" style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif; color: #bc0000; font-size: 48px; line-height: 55px; font-weight: 700; letter-spacing: -1.5px;">河池学院党支部</span>
                       <!-- </a> -->
                       </font>
                       <div style="height: 73px; line-height: 73px; font-size: 71px;">&nbsp;</div>
                     </td>
                   </tr>
                 </tbody></table>
                 
                 <HR style="FILTER: alpha(opacity=100,finishopacity=0,style=3)" width="80%" color=#bc0000 SIZE=3>
   
                 <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                   <tbody><tr>
                     <td align="left" valign="top">
                       <font face="Nunito, sans-serif" color="#1a1a1a" style="font-size: 52px; line-height: 60px; font-weight: 300; letter-spacing: -1.5px;">
                         <span style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif;  font-size: 30px; line-height: 60px; font-weight: 700; letter-spacing: -1.5px;">您好,</span>
                         <span style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif; color: green; font-size: 30px; line-height: 60px; font-weight: 700; letter-spacing: -1.5px;">' . $name . '</span>

                         <span style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif;  font-size: 30px; line-height: 60px; font-weight: 700; letter-spacing: -1.5px;">' . $call . '</span>
                       </font>
                       <div style="height: 33px; line-height: 33px; font-size: 31px;">&nbsp;</div>
                       <font face="Nunito, sans-serif" color="#585858" style="font-size: 24px; line-height: 32px;">
                         <span style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif; color: #585858; font-size: 24px; line-height: 32px;">以下6位数字是邮箱验证码，请在网站上填写以通过验证</span>
                       </font>
                       <div style="height: 18px; line-height: 33px; font-size: 31px;">&nbsp;</div>
                       <font face="Nunito, sans-serif" color="#585858" style="font-size: 24px; line-height: 32px;">
                         <span style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif; color: #aaaaaa; font-size: 16px; line-height: 32px;">(如果您从未请求发送邮箱验证码，请忽略此邮件)</span>
                       </font>
                       <div style="height: 33px; line-height: 33px; font-size: 31px;">&nbsp;</div>
                       <table class="mob_btn" cellpadding="0" cellspacing="0" border="0" style="background: #6777ef; border-radius: 4px;">
                         <tbody><tr>
                           <td align="center" valign="top">
                             <span style="display: block; border: 1px solid #6777ef; border-radius: 0px; padding: 6px 12px; font-family: Nunito, Arial, Verdana, Tahoma, Geneva, sans-serif; color: #ffffff; font-size: 20px; line-height: 30px; text-decoration: none; white-space: nowrap; font-weight: 600;">
                               <font face="Nunito, sans-serif" color="#ffffff" style="font-size: 20px; line-height: 30px; text-decoration: none; white-space: nowrap; font-weight: 600;">
                                 <span style="font-family: Nunito, Arial, Verdana, Tahoma, Geneva, sans-serif; color: #ffffff; font-size: 20px; line-height: 30px; text-decoration: none; white-space: nowrap; font-weight: 600;">' . $code . '</span>
                               </font>
                             </span>
                           </td>
                         </tr>
                       </tbody></table>
                       <div style="height: 75px; line-height: 75px; font-size: 73px;">&nbsp;</div>
                     </td>
                   </tr>
                 </tbody></table>
   
                 <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100% !important; min-width: 100%; max-width: 100%; background: #f3f3f3;">
                   <tbody><tr>
                     <td align="center" valign="top">
                       <div style="height: 34px; line-height: 34px; font-size: 32px;">&nbsp;</div>
                       <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                         <tbody><tr>
                           <!-- <td align="center" valign="top">
                             <div style="height:12px; line-height: 34px; font-size: 32px;">&nbsp;</div>
                             <font face="Nunito, sans-serif" color="#868686" style="font-size: 17px; line-height: 20px;">
                               <span style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif; color: #868686; font-size: 17px; line-height: 20px;">2020 © 加速狗. All Rights Reserved.</span>
                             </font>
                             <div style="height: 3px; line-height: 3px; font-size: 1px;">&nbsp;</div>
                             <font face="Nunito, sans-serif" color="#1a1a1a" style="font-size: 17px; line-height: 20px;">
                               <span style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif; color: #1a1a1a; font-size: 17px; line-height: 20px;"><a href="https://www.jiasu.dog" target="_blank" style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif; color: #1a1a1a; font-size: 17px; line-height: 20px; text-decoration: none;" rel="noopener">访问官网</a> &nbsp; | &nbsp; <a href="https://www.jiasu.dog/user/" target="_blank" style="font-family: Nunito, Arial, Tahoma, Geneva, sans-serif; color: #1a1a1a; font-size: 17px; line-height: 20px; text-decoration: none;" rel="noopener">用户中心</a></span>
                             </font>
                             <div style="height: 35px; line-height: 35px; font-size: 33px;">&nbsp;</div>
                           </td> -->
                         </tr>
                       </tbody></table>
                     </td>
                   </tr>
                 </tbody></table>
   
               </td>
               <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
             </tr>
           </tbody></table>
           
         </td>
       </tr>
     </tbody></table>
   
   
   
   <style type="text/css">.qmbox style, .qmbox script, .qmbox head, .qmbox link, .qmbox meta {display: none !important;}</style></div></div><!-- --><style>#mailContentContainer .txt {height:auto;}</style>  </div>';


    return $email_html_alibaba;
}
