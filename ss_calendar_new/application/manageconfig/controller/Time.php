<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:21
 */

namespace app\manageconfig\controller;


use app\common\controller\Common;
use app\manageconfig\model\ScheduleTime as ScheduleTime;
use think\Request;

class Time extends Common
{
	/**
     * @purpose：
     *  添加每天时间段
     * @Author 第12组 黄捷
     * @Date 2019-4-7
     * 
     */
    public function index(){
        // 显示未删除的时间段，is_delete标记为零         
    	$list = new ScheduleTime();
        $list = $list->getAllTime(); 
        // 使用模板渲染的foreach标签         
        $this->assign('list_time_table',$list);
        return $this->fetch();
    }


    //编辑时间
    public function timeChange()
    {
        // 获取post数据，考虑权限控制吗？如果有机器无限制post或者get会怎样？网站的安全控制策略
        $id = Request::instance()->param('id');
        $name = Request::instance()->param('name');        
        $order = Request::instance()->param('order'); 
        $time_rec = new ScheduleTime();
        $check = new ScheduleTime();
        $check_res = $check->where(['name'=>$name,'is_delete'=>0])
                            ->find();
        // 查询符合条件的时段描述
        $time_rec = $time_rec->where('id',$id)
                            ->find();
        // var_dump($check_res->id);
        // echo "+++++++++++++++++++++";
        // var_dump($time_rec->id);
        if((!empty($check_res))&&$check_res->id != $time_rec->id)
            return "重复的时间描述";         
        $time_rec->name = $name;
        $time_rec->time_order = $order;
        // $time_rec->update_time = date("Y-m-d H:i:s");
        $res = $time_rec->save();
        if($res)
        {
            return "更新成功";
        }
        return "更新失败";        
    }

    //删除时间
    public function timeDelete()
    {
        // 获取post数据
        $id = Request::instance()->param('id');
        $name = Request::instance()->param('name');

        $time_rec = new ScheduleTime();
        // 查询符合条件的时段描述
        $time_rec = $time_rec->where('id',$id)
                            ->find();
        $time_rec->is_delete = 1;
        $time_rec->delete_time = date("Y-m-d H:i:s");
        $res = $time_rec->save();
        if($res)
        {
            return "删除成功";
        }
        return "删除失败";        
    }

    //编辑时间
    public function timeAdd()
    {
        // 获取post数据        
        $name = Request::instance()->param('name');
        $order = Request::instance()->param('order');
        $time_rec = new ScheduleTime(); 
        $check = new ScheduleTime();
        $check_res = $check->where(['name'=>$name,'is_delete'=>0])->select();
        if($check_res)
            return "重复的时间描述";    
        $time_rec->name = $name;
        $time_rec->time_order = $order;
        $time_rec->is_delete = 0;
        // create_time数据库自己更新
        $res = $time_rec->save();
        if($res)
        {
            return "更新成功";
        }
        return "更新失败";        
    }
}