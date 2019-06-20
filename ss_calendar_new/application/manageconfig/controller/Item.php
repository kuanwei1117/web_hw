<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:24
 */

namespace app\manageconfig\controller;

use app\common\controller\Common;
use app\logmanage\model\Log as LogModel;
use think\Session; //引入日誌接口

class Item extends Common
{
    public function index(){
        $model = model('ScheduleItem');
        $scheduleItems = $model->getAllItems();
        $this->assign('scheduleItems',$scheduleItems);
        return $this->fetch();
    }
    //添加事项的方法
    public function  addScheduleItem()
    {
        //写入日志代码
        $modelLog = new LogModel();
        $uid = ADMIN_ID;
        $type =2;
        $is_manage = 1;
        $table ='schedule_item';
        $des = $_POST['des'];
        $model = model('ScheduleItem');
        $isHasSame = $model->getItemByName($des);
        if ($isHasSame == null) {
            $res = $model->insertScheduleItem($des);
            if($res){
                $field =[$res];
                $modelLog->recordLogApi($uid,$type,$is_manage,$table,$field);
                $this->success("新增成功");
            }
            else{
                $this->error("添加失败，请重新尝试");
            }
        }
        else{
            $this->error("已经存在同名有效或无效事项");
        }
    }
    //删除事项
    public function deleteScheduleItem(){
        $modelLog = new LogModel();
        $uid = ADMIN_ID;
        $type =4;
        $is_manage = 1;
        $table ='schedule_item';
        $id = $_POST['id'];
        $field=[$id];
        $model = model('ScheduleItem');
        $res = $model->deleteScheduleItem($id);
        if($res == 1){
            $modelLog->recordLogApi($uid,$type,$is_manage,$table,$field);
            $this->success("删除成功");
        }
        else{
            $this->error(  "删除失败，请重新操作!");
        }
    }
    //恢复事项的方法3
    public function updateScheduleItem(){
        $modelLog = new LogModel();
        $uid = ADMIN_ID;
        $type =4;
        $is_manage = 1;
        $table ='schedule_item';
        $id = $_POST['id'];
        $field=[$id];
        $model = model('ScheduleItem');
        $res = $model->updateScheduleItemState($id);
        if($res == 1){
            $modelLog->recordLogApi($uid,$type,$is_manage,$table,$field);
            $this->success("恢复成功");
        }
        else{
            $this->error(  "恢复失败，请重新操作!");
        }
    }
    //编辑事项方法
    public function editScheduleItem(){
        $modelLog = new LogModel();
        $uid = ADMIN_ID;
        $type =3;
        $is_manage = 1;
        $table ='schedule_item';
        $id = $_POST['id'];
        $des = $_POST['des'];
        $yuanDes = $_POST['yuanDes'];
        $field =[$id =>[$yuanDes,$des]];
        $model = model('ScheduleItem');
        $isSame = $model->getItemByName($des);
        if($isSame ==null){
            $res = $model->updateScheduleItem($id,$des);
            if($res ==1){
                $modelLog->recordLogApi($uid,$type,$is_manage,$table,$field);
                $this->success("编辑成功!");
            }
            else{
                $this->error(  "编辑失败，请重新操作!");
            }
        }
        else{
            $this->error(  "修改事项名称与已有重复，请重新修改!");
        }
    }
    function unicodeDecode($data)
    {
        function replace_unicode_escape_sequence($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }

        $rs = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $data);

        return $rs;
    }
}