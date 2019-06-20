<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/16
 * Time: 16:22
 */

namespace app\msgmanage\model;
use think\Model;
use think\Db;

class Whitelist extends Model{
    public function getinfo(){

        //页面table初始化，得到所有白名单人员的信息
        $info = Db::table('white_list')
            ->alias(['user_info' => 'ui', 'white_list' => 'w'])
            ->where('w.is_delete',0)
            ->where('ui.is_delete',0)
            ->join('user_info','ui.id = w.user_id')
            ->field('w.id,ui.name as ui_name,ui.work_id,ui.type_id,ui.depart_id,ui.position_id')
            ->select();
        foreach ($info as $key=>$value){
            //判断部门是否存在
            $depart_exist = Db::table('user_depart')->where('id',$info[$key]['depart_id'])->where('is_delete',0)->find();
            if ($depart_exist == null){
                $info[$key]['ud_name'] = '空';
            }else{
                $info[$key]['ud_name'] = Db::table('user_depart')->where('id',$info[$key]['depart_id'])->value('name');
            }
            if ($info[$key]['depart_id'] == 0){
                $info[$key]['ud_name'] = '学院';
            }
            //判断职位是否存在
            $position_exist = Db::table('user_position')->where('id',$info[$key]['position_id'])->where('is_delete',0)->find();
            if($position_exist == null){
                $info[$key]['up_name'] = '空';
            }else{
                $info[$key]['up_name'] = Db::table('user_position')->where('id',$info[$key]['position_id'])->value('name');
            }
        }

        return $info;
    }


    //添加人员时判断工号是否在user_info中已存在且有效
    public function exist_work_id($work_id){
        $isexist = Db::table('user_info')->where('work_id',$work_id)->where('is_delete',0)->find();
        if ($isexist==null){
            return true;
        }else{
            return false;
        }
    }
    //添加人员时判断工号是否在white_list中已存在且有效
    public function exist_white_list($work_id){
        $user_id = Db::table('user_info')->where('work_id',$work_id)->where('is_delete',0)->value('id');
        $exist = Db::table('white_list')->where('user_id',$user_id)->where('is_delete',0)->find();
        return $exist;
    }
    //添加人员
    public function add($work_id){
        $user_id = Db::table('user_info')->where('work_id',$work_id)->where('is_delete',0)->value('id');
        $is_add = Db::table('white_list')->data(['user_id'=>$user_id])->insert();
        return $is_add;
    }

    
    //---------------------------------------------------------------
    /*
    创建： 翁嘉进
    功能： 白名单删除操作
    实现： 1.连接表——白名单 
           2.删除数据
           3.返回结果
    */
    public function delwhitelist($data){
        $is_delete = Db::table('white_list')->where('id',$data['del_id'])
            ->update(['is_delete' => 1]);
        return $is_delete;
    }
    
    //---------------------------------------------------------------
    /*
    创建： 翁嘉进
    功能： 清空白名单操作
    实现： 1.连接表——白名单 
           2.查询带清空的数据
           3.软清空白名单
           4.记录清空个数 
           5.返回结果
    */
    public function clearwhitelist(){
    	$list = db("white_list")->where("is_delete",0)->select();
        $is_clear = 0;
        $clear_ids = "[";
    
        foreach($list as $data){
            $postdata = [
                        "is_delete" => 1,
                        ];
            $cul = db("white_list")->where("id",$data["id"])->update($postdata);
            $is_clear += $cul;
            $clear_ids = $clear_ids . $data["id"] . ", ";
        }
        $clear_ids = $clear_ids . "]";
        $ret_date = [
            "clear_ids" => $clear_ids,
            "is_clear" => $is_clear,
        ];
        return $ret_date;
    }
    
    //---------------------------------------------------------------
    /*
    responser: 陈国强
    Created：2019/05/15
    insertAllUser($data) ： 向 user_info 数据表插入信息
    findUserByWorkId($workId)： 通过工号查找该用户是否存在
    */
    public function insertAllUser($data) {
        return Db::table('user_info')->insertAll($data);
    }
    
    public function findUserByWorkId($workId) {
        return Db::table('user_info')
            ->where('work_id', $workId)
            ->where('is_delete', 0)
            ->find();
    }
}

    //---------------------------------------------------------------