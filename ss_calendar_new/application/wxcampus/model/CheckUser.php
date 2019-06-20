<?php


namespace app\wxcampus\model;
use think\Db;
use think\Model;

class CheckUser extends Model
{
    /**
     * @purpose：

     * @Author 第09组 沈安强
     * @Date 2019-4-25
     *
     */
    protected $table = 'user_info';
    //检查user_info表内有没有该用户
    public function checkUser($number){
        $res = Db::table("user_info")->where('work_id ='.$number)->select();
        return $res;
    }

    public function addUser($name,$number){
        Db::table('user_info')
            ->data(['name'=> $name,'work_id'=>$number,'type_id'=>0,'depart_id'=>0,
            'position_id'=>50,'is_delete'=>0])->insert();


    }

    //获取对应学号的user_id;
    public function getUserId($number){

        $res = Db::table('user_info')->where('work_id ='.$number)->column('id');
        return $res[0];
    }
}
