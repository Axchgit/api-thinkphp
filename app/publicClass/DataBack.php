<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2021-04-12 00:45:03
 * @LastEditors: xch
 * @Description: 
 * @FilePath: \vue-framed:\wamp64\www\api-thinkphp\app\publicClass\DataBack.php
 */

namespace app\publicClass;
// namespace app\model;
// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\facade\Db;


class DataBack
{


        //查看目录内的文件和目录，并按生成时间排序
        function getDirContent($file_path)
        {
            //要查看的目录
            // $file_path = '../extend/';
    
            //判断 Mac 是否有 DS_Store，拉取文件是否有.gitkeep、.keep，并排除
            $files = [];
            $file = scandir($file_path, 1);
            if (!empty($file)) {
                foreach ($file as $k => $v) {
                    if ($v != '.' && $v != '..' && $v != '.DS_Store' && $v != '.gitkeep' && $v != '.keep'&& $v != '.gitginore') {
                        $files[] = $v;
                    }
                }
            }
    
            $list = [];
            if (is_array($files)) {
                foreach ($files as $k => $v) {
                    $filesize = filesize($file_path . $v);
                    if ($filesize < 1024) {
                        $size = sprintf("%01.2f", $filesize) . "B";
                    } elseif ($filesize < 1024 * 1024) {
                        $size = sprintf("%01.2f", ($filesize / 1024)) . "KB";
                    } elseif ($filesize < 1024 * 1024 * 1024) {
                        $size = sprintf("%01.2f", ($filesize / (1024 * 1024))) . "MB";
                    } elseif ($filesize < 1024 * 1024 * 1024 * 1024) {
                        $size = sprintf("%01.2f", ($filesize / (1024 * 1024 * 1024))) . "GB";
                    }
                    // explode("\\",$file_path);
                    
                    // substr($v,0,strpos($v, '_2'));
                    // $new_file_path = stripslashes($file_path);
                    $list[] = [
                        'index' => $k,
                        'file' => $file_path . $v,
                        // 'dbname' => explode("-", $v)[0],
                        'dbname' => substr($v,0,strpos($v, '_2')),
                        'size' => $size,
                        'create_time' => filemtime($file_path . $v),
                        'create_date' => date('Y-m-d H:i:s', filemtime($file_path . $v)),
                    ];
                }
            }
    
            //根据文件和目录生成时间按倒序排列
            $list = $this->arraySort($list, 'create_time', SORT_DESC);
            if (empty($list)) {
                return [false];
            } else {
                return [true, $list];
            }
    
            // return $this->create($list,'成功');
    
            // echo '<pre>';
            // print_r($list);
            // die;
            // echo '</pre>';
        }
    
        /**
         * 二维数组根据某个字段排序
         * @param array $array	要排序的数组
         * @param string $keys	要排序的键字段
         * @param string $sort	排序类型: SORT_ASC 升序, SORT_DESC 降序
         * @return array 		排序后的数组
         */
        public function arraySort($array, $keys, $sort = SORT_DESC)
        {
            $keysValue = [];
            foreach ($array as $k => $v) {
                $keysValue[$k] = $v[$keys];
            }
            array_multisort($keysValue, $sort, $array);
            return $array;
        }
    

    
    /**
     * @description: 数据库备份
     *@param string $dbname

     *@param bool $backupFile

     *@return {*}
     */
    public function backupSql($dbname = 'ep_api', string $backupFile = '+_+')
    {
        $mysqldump_path = config('database.backup.mysql_bin_path');
        // $dbhost = '127.0.0.1';config
        $dbhost = config('database.connections.mysql.hostname');
        $dbuser = config('database.connections.mysql.username');
        $dbpass = config('database.connections.mysql.password');

        
        if ($backupFile === '+_+') {
            $backupFile = config('database.backup.databack_path') . $dbname . '_' . date("Y-m-d_His") . '.sql';
        }
        if ($dbpass === '') {
            exec($mysqldump_path . "mysqldump -h $dbhost -u$dbuser  $dbname > $backupFile");
        } else {
            exec($mysqldump_path . "mysqldump -h $dbhost -u$dbuser -p$dbpass  $dbname > $backupFile");
        }
        return  stripslashes($backupFile);
    }
    /**
     * @description: 数据库还原
     *@param string $dbname 数据库名称
     *@param string $backupFile 
     *@param string $mysqldump_path


     *@return {*}
     */
    public function restoreSql($backupFile, $dbname = 'ep_api' )
    {
        $mysqldump_path = config('database.backup.mysql_bin_path');

        // $dbhost = '127.0.0.1';config
        $dbhost = config('database.connections.mysql.hostname');
        $dbuser = config('database.connections.mysql.username');
        $dbpass = config('database.connections.mysql.password');
        $backupFile = stripslashes($backupFile);

        
        if ($dbpass === '') {
            exec($mysqldump_path . "mysql -h $dbhost -u$dbuser  $dbname < $backupFile");
        } else {
            exec($mysqldump_path . "mysql  -h $dbhost -u$dbuser -p$dbpass  $dbname < $backupFile");
        }
        return $backupFile;
    }




    // //管理员登录验证
    // public function findAdmin($name, $password)
    // {
    //     return $this->where('admin_name', $name)->where('admin_pass', $password)->find();
    // }

    // /**
    //  * @description: 把生成的登录码保存进数据库
    //  * @param {type} 
    //  * @return {type} 
    //  */
    // public function saveLogcode($name, $log_code)
    // {
    //     $admin_uuid = $this->where('admin_name',$name)->value('uuid');        
    //     $data = [
    //         'uuid' => $admin_uuid,
    //         'code' => $log_code,
    //         'msg' => '登录码'
    //     ];
    //     //跨表数据库操作
    //     return Db::table('temp_code')->insert($data);
    //     // $admin->code = $log_code;
    // }
    //     /**
    //  * @description: 删除此用户之前的登录码
    //  * @param {type} 
    //  * @return {type} 
    //  */
    // public function deleteLogcode($name)
    // {
    //     // try{

    //     // }

    //     $admin_uuid = $this->where('admin_name',$name)->value('uuid');
    //     if(!empty($admin_uuid)){
    //         return Db::table('temp_code')->where('uuid',$admin_uuid)->delete();
    //     }else{
    //         return false;
    //     }
    //     // $res = Db::table('temp_code')->where('uuid',$admin_uuid)->selectOrFail();
    //     // if($res){
    //     //     return Db::table('temp_code')->where('uuid',$admin_uuid)->delete();;
    //     // }else{
    //     //     return true;
    //     // }        
    //     // 知识点:跨表数据库操作
    //     // $admin->code = $log_code;
    // }

    // /**
    //  * @description: 查询管理员邮箱
    //  * @param {type} 
    //  * @return {type} 
    //  */
    // public function selectMail($name)
    // {

    //     return $this->where('admin_name', $name)->value('admin_email');
    // }


    //over
}
