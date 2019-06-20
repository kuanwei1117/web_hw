<?php
/**
 * Created by PhpStorm.
 * User: yym
 * Date: 2019-04-07
 * Time: 21:58
 */
namespace app\manageconfig\model;
use think\Model;
use think\Db;

class ScheduleItem extends Model
{
    /**
     * 功能：获取schedule_item表中的目前最大id号
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
 public function getMaxId(){
     $maxId = Db::name('schedule_item')
         ->order('id desc')
         ->limit(1)
         ->select();
     return $maxId;
 }

    /**
     * 功能：插入新的事项数据到数据库
     * @param $des
     * @return int|string
     */
 public function insertScheduleItem($des){
     $data = ['name' => $des, 'is_delete' => 0,'update_time'=> date('Y-m-d H:i:s',time())];
     $res = Db::name('schedule_item')->insertGetId($data);
     return $res;
 }

    /**
     * 功能：根据事项描述从数据库获取事项
     * @param $des
     */
 public function getItemByName($des){
     $nameItem = Db::name('schedule_item')
         ->where('name',$des)
         ->find();
     return $nameItem;
 }

    /**
     * 功能获取数据库中所有有效的事项数据
     */
 public function getAllItems(){
     $allItems = Db::name('schedule_item')
         ->order('is_delete')
         ->select();
     return $allItems;
 }

    /**
     * 功能：设置某条事项数据无效
     * @param $id
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
 public function deleteScheduleItem($id){
     $data = ['is_delete' => 1,'update_time'=> date('Y-m-d H:i:s',time()),'delete_time'=> date('Y-m-d H:i:s',time())];
     $res = Db::name('schedule_item')
         ->where('id',$id)
         ->update($data);
     return $res;
 }
 //更新事项
    public function updateScheduleItem($id,$des){
     $data = ['name' => $des,'update_time'=> date('Y-m-d H:i:s',time())];
     $res = Db::name('schedule_item')
         ->where('id',$id)
         ->update($data);
        return $res;
    }
 //更新事项的状态
    public function updateScheduleItemState($id){
        $data = ['is_delete' => 0,'update_time'=> date('Y-m-d H:i:s',time()),'delete_time'=> date('Y-m-d H:i:s',time())];
        $res = Db::name('schedule_item')
            ->where('id',$id)
            ->update($data);
        return $res;
    }
}