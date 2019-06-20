<?php
namespace app\login\model;
use think\Model;
use think\Db;
use app\logmanage\model\Log as LogModel;

class Mobile extends Model
{
    /**
     * 吴欣雨
     * 功能：将管理员手机号插入到数据库
     * @param $id, $mobile
     * @return int|string
     */
    public function addMobile($id, $mobile)
    {
        //$data=Db::name('manage_info')->where('id', $id)->field("telephone")->select();
        //var_dump($data);
         $model=new LogModel();
         $uid=$id;
          $type=3;
          $is_manage=1;
           $table='manage_info';
$field = [
'22'=>[
'telephone'=> ['', $mobile]
]
];
$model->recordLogApi ($uid, $type, $is_manage, $table, $field);
        $res = Db::name('manage_info')->where('id', $id)->update(['telephone' => $mobile]);
        return $res;
    }

    /**
     * 吴欣雨
     * 功能：在管理员数据库中查找手机号，如果手机号存在，则返回手机号，如果手机号不存在，返回false
     * @param $telephone
     * @return boolean
     */
    public function hasMobile($telephone)
    {
        $data = Db::name('manage_info')->where('telephone', $telephone)->select();//查询对应管理员的记录
        if (empty($data)) return false;
        else return true;
    }
    public function alreadyMobile($id)
    {
        $data = Db::name('manage_info')->where('id', $id)->field('telephone')->select();//查询对应管理员的记
      return $data;
    }
}

