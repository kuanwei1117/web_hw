<?php
namespace app\querystatistics\controller;

use app\common\controller\Common;
use think\Controller;
use think\Db;
use think\Request;

/* @purpose：
     *  修改某个用户的日程信息
     * @Author 第1组 林奕君
     * @Date 2019-4-11
     * 首先，得到某个用户的日程信息，传前端，即函数getSchedule()
     * 其次，前端界面修改，提交信息
     * 最后，提交的信息传给数据库，即函数modifySchedule()
*/
class Modifyschedule extends Common
{
    public function index()
    {
        $list = Db::table('schedule_info')
        ->alias('si')
        ->join('user_info ui','si.user_id = ui.id')
        ->join('schedule_time st','si.time_id = st.id')
        ->join('schedule_place sp','si.place_id = sp.id')
        ->join('schedule_item sm','si.item_id = sm.id')
        ->join('user_position up','ui.position_id = up.id')
        ->field('si.id as id,
        si.user_id as user_id,
        date,time_id, place_id, item_id, position_id,
        ui.name as user_info_name, 
        st.name as schedule_time_name, 
        sp.name as schedule_place_name, 
        sm.name as schedule_item_name,
        up.name as user_position_name')
        ->where('si.is_delete','=',0)
        ->where('ui.is_delete','=',0)
        ->where('st.is_delete','=',0)
        ->where('sp.is_delete','=',0)
        ->where('sm.is_delete','=',0)
        ->where('up.is_delete','=',0)
        ->select();
        $this->assign('user_schedule',$list);
        //dump($list); //打印看输出的列数
        //dump($list);
        $new_time = Db::table('schedule_time')
        ->where('is_delete','=',0)
        ->select();
        $this->assign('user_time',$new_time);
        $new_place = Db::table('schedule_place')
        ->where('is_delete','=',0)
        ->select();
        $this->assign('user_place',$new_place);
        $new_item = Db::table('schedule_item')
        ->where('is_delete','=',0)
        ->select();
        $this->assign('user_item',$new_item);
        return $this->fetch();

    }
    function modifySchedule()
    {   
        /*
        $user_info_name=$_POST['user_info_name']; 
        $schedule_time_name=$_POST['schedule_time_name'];
        $schedule_place_name=$_POST['schedule_place_name']; 
        $schedule_item_name=$_POST['schedule_item_name'];
        $user_id=$_POST['user_id'];
        $date=$_POST['date'];
        $time_id=$_POST['time_id'];
        $place_id=$_POST['place_id'];
        $item_id=$_POST['item_id'];*/
        $id = Request::instance()->param('id');
        $user_info_name = Request::instance()->param('user_info_name');
        $schedule_time_name = Request::instance()->param('schedule_time_name');
        $schedule_place_name = Request::instance()->param('schedule_place_name');
        $schedule_item_name=Request::instance()->param('schedule_item_name');
        //var_dump($schedule_place_name);
        //return;
        $user_id = Request::instance()->param('user_id');
        $time_id = Request::instance()->param('time_id');
        $place_id = Request::instance()->param('place_id');
        $item_id = Request::instance()->param('item_id');
        $date = Request::instance()->param('date');
        //dump($date);
        //console.log(order);
        $note=$user_info_name.$schedule_time_name.$schedule_place_name.$schedule_item_name;
        try{
            $schedule_info = Db::table('schedule_info')->where('id', $id)->update([
                'date' => $date,
                'time_id' => $time_id,
                'place_id' =>$place_id,
                'item_id' =>$item_id,
                'note' => $note
            ]);
            
        }catch(Exception $e){
            var_dump($e);
        }

        if ($schedule_info)
            return "更新成功";
        else
            return "更新失败";
        
    }
}
?>