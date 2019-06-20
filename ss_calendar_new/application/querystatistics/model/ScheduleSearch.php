<?php
namespace app\querystatistics\model;
use think\Console;
use think\Model;
use think\Db;


class ScheduleSearch extends Model
{
    public function searchAllInfo(){
        {
            $info = Db::table('schedule_info')
                ->alias(['schedule_info' => 'a', 'user_info' => 'b', 'user_position' => 'c', 'schedule_time' => 'd', 'schedule_place' => 'e', 'schedule_item' => 'f'])
                ->where('a.is_delete',0)
				->where('b.is_delete',0)
				->where('c.is_delete',0)
				->where('d.is_delete',0)
				->where('e.is_delete',0)
				->where('f.is_delete',0)
                ->join('user_info','a.user_id = b.id')
                ->join('user_position','b.position_id = c.id')
                ->join('schedule_time','a.time_id = d.id')
                ->join('schedule_place','a.place_id = e.id')
                ->join('schedule_item','a.item_id = f.id')
                ->field('a.id, b.name as name, c.name as position, date,d.name as time, e.name as place, f.name as item, b.id as userid')
                ->order('date desc')
				->select();
            return $info;
        }
    }

    public function searchPartialInfo($start_time,$terminal_time){
        $time1 = date('Y-m-d',strtotime($start_time));
        $time2 = date('Y-m-d',strtotime($terminal_time));
        $info = Db::table('schedule_info')
            ->alias(['schedule_info' => 'a', 'user_info' => 'b', 'user_position' => 'c', 'schedule_time' => 'd', 'schedule_place' => 'e', 'schedule_item' => 'f'])
            ->where('a.is_delete',0)
            ->where('b.is_delete',0)
            ->where('c.is_delete',0)
            ->where('d.is_delete',0)
            ->where('e.is_delete',0)
            ->where('f.is_delete',0)
            ->where('a.date','between',[$time1,$time2])
            ->join('user_info','a.user_id = b.id')
            ->join('user_position','b.position_id = c.id')
            ->join('schedule_time','a.time_id = d.id')
            ->join('schedule_place','a.place_id = e.id')
            ->join('schedule_item','a.item_id = f.id')
            ->field('a.id, b.name as name, c.name as position, date,d.name as time, e.name as place, f.name as item, b.id as userid')
            ->order('date desc')
            ->select();
        return $info;
    }

    public function searchThreeWeekInfo(){
        {
            $now=date('Y-m-d',time());  //查询当前日期
            $this_week_number = date('W',strtotime($now));
            $w = date('w'); //获取当天位于一周的第几天
            $first=1;
            $this_week_start = date('Y-m-d',strtotime("now -".($w ? $w - $first : 6).'days'));  //查询本周一日期
            $last_week_start = date('Y-m-d',strtotime("$this_week_start -7 days"));
            $next_week_end = date('Y-m-d',strtotime("$this_week_start +13 days"));
            $info = Db::table('schedule_info')
                ->alias(['schedule_info' => 'a', 'user_info' => 'b', 'user_position' => 'c', 'schedule_time' => 'd', 'schedule_place' => 'e', 'schedule_item' => 'f'])
                ->where('a.is_delete',0)
                ->where('b.is_delete',0)
                ->where('c.is_delete',0)
                ->where('d.is_delete',0)
                ->where('e.is_delete',0)
                ->where('f.is_delete',0)
                ->whereTime('a.date','between',[$last_week_start, $next_week_end])
                ->join('user_info','a.user_id = b.id')
                ->join('user_position','b.position_id = c.id')
                ->join('schedule_time','a.time_id = d.id')
                ->join('schedule_place','a.place_id = e.id')
                ->join('schedule_item','a.item_id = f.id')
                ->field('a.id, b.name as name, c.name as position, date,date_format(date,\'%m-%d\') as partial_date,date_format(date,\'%u\') as week_number,date_format(date,\'%w\') as week,d.name as time, e.name as place, f.name as item, b.id as userid')
                ->order('date desc')
                ->select();
            return $info;
        }
    }
}