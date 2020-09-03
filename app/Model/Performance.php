<?php
/*
 * @Author: xch
 * @Date: 2020-08-15 12:01:16
 * @LastEditTime: 2020-09-04 02:33:40
 * @LastEditors: xch
 * @Description: 员工信息
 * @FilePath: \epdemoc:\wamp64\www\api-thinkphp\app\Model\Performance.php
 */

namespace app\model;

// use PHPExcel_IOFactory;

// use think\Db;
use think\Model;
use think\model\concern\SoftDelete;
use think\facade\Db;


class Performance extends Model
{
    // TODO:软删除
    // use SoftDelete;
    // protected $deleteTime = 'delete_time';

    public function insertPerformance($uuid, $goods_id)
    {
        try {
            $this->save(['uuid' => $uuid, 'goods_id' => $goods_id]);
            return true;
        } catch (\Exception  $e) {
            return $e;
        }
    }

    //员工查询业绩
    public function selectPerformance($uuid, $key, $value, $list_rows = 10, $isSimple = false, $config = '')
    {
        switch ($key) {
                // case 'order_id':
                //     $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                //     break;
            case 'goods_id':
                $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                break;
            case 'audit_status':
                $data = $this->where('uuid', $uuid)->where($key, $value)->paginate($list_rows, $isSimple, $config);
                break;
                // case 'audit_status':
                //     $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                //     break;
                // case 'shop_name':
                //     $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                //     break;
            default:
                $data = $this->where('uuid', $uuid)->paginate($list_rows, $isSimple, $config);
                // $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                //     ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                //     // ->view('Score', 'score', 'Score.user_id=Profile.id')
                //     ->where('uuid', $uuid)
                //     ->paginate($list_rows, $isSimple, $config);
        }
        if (empty($data)) {
            return false;
        } else {
            return $data;
        }
    }

    public function softDeletePerformance($uuid = '',$id){
        try{
            $this->where('uuid',$uuid)->where('id',$id)->delete();
            return true;
        } catch (\Exception  $e) {
            return $e;
        }
    }

        //员工查询推广商品
        public function selectPerformanceGoods($uuid, $key, $value, $list_rows = 10, $isSimple = false, $config = '')
        {
            switch ($key) {
                    // case 'order_id':
                    //     $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                    //     break;
                case 'goods_id':
                    $data = $this->where($key, $value)->paginate($list_rows, $isSimple, $config);
                    break;
                case 'audit_status':
                    $data = $this->where('uuid', $uuid)->where($key, $value)->paginate($list_rows, $isSimple, $config);
                    break;
                    // case 'audit_status':
                    //     $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                    //     break;
                    // case 'shop_name':
                    //     $data = $this->whereLike($key, '%' . $value . '%')->paginate($list_rows, $isSimple, $config);
                    //     break;
                default:
                    // $data = $this->where('uuid', $uuid)->paginate($list_rows, $isSimple, $config);
                    $data = Db::view('performance', 'uuid,goods_id,audit_status,create_time')
                        ->view('goods', 'id,goods_name,shop_name', 'goods.goods_id=performance.goods_id')
                        // ->view('Score', 'score', 'Score.user_id=Profile.id')
                        ->where('uuid', $uuid)
                        ->paginate($list_rows, $isSimple, $config);
            }
            if (empty($data)) {
                return false;
            } else {
                return $data;
            }
        }



    //结束
}
