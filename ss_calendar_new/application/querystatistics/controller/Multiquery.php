<?php
namespace app\querystatistics\controller;
use app\common\controller\Common;

use think\Controller;
use think\Db;
use think\Request;


class Multiquery extends Common
{
    public function index()
    {
        $info = Db::table('schedule_info')
            ->alias(['schedule_info' => 'a', 'user_info' => 'b', 'user_position' => 'c', 'schedule_time' => 'd', 'schedule_place' => 'e', 'schedule_item' => 'f'])
            ->where('a.is_delete',0)
            ->join('user_info','a.user_id = b.id')
            ->join('user_position','b.position_id = c.id')
            ->join('schedule_time','a.time_id = d.id')
            ->join('schedule_place','a.place_id = e.id')
            ->join('schedule_item','a.item_id = f.id')
            ->field('a.id, b.name as name, c.name as position, a.date as date, d.name as time, e.name as place, f.name as item, b.id as userid')
            ->select();
        $zero1=date("y-m-d");
        $finalres = array();
        foreach ($info as $singlearr) {
            if(strtotime($zero1) <= strtotime($singlearr['date'])){
                array_push($finalres,$singlearr); 
            }
        }
        $all_dates = array_column($finalres,'place');
        array_multisort($all_dates,SORT_ASC,$finalres);
        $all_dates = array_column($finalres,'date');
        array_multisort($all_dates,SORT_ASC,$finalres);
        $this->assign('info',$finalres);
        return $this->fetch('search');
    }

    public function searchnames(){
        $mydata = input('post.');
        if (empty($mydata['names'])){
            return $this->index();
        }

        $names = explode(" ",$mydata['names']);
        $nameids = array();
        foreach ($names as $name) {
            $buffer = Db::table('user_info')
            ->where('user_info.name',$name)
            ->field('id')
            ->select();
            if (!empty($buffer)){
                array_push($nameids,$buffer[0]['id']);
            }
        }
        $info = Db::table('schedule_info')
            ->alias(['schedule_info' => 'a', 'user_info' => 'b', 'user_position' => 'c', 'schedule_time' => 'd', 'schedule_place' => 'e', 'schedule_item' => 'f'])
            ->where('a.is_delete',0)
            ->join('user_info','a.user_id = b.id')
            ->join('user_position','b.position_id = c.id')
            ->join('schedule_time','a.time_id = d.id')
            ->join('schedule_place','a.place_id = e.id')
            ->join('schedule_item','a.item_id = f.id')
            ->field('a.id, b.name as name, c.name as position, a.date as date, d.name as time, e.name as place, f.name as item, b.id as userid')
            ->select();
        $zero1=date("y-m-d");
        $finalres = array();
        foreach ($nameids as $nameid) {
            foreach ($info as $singlearr) {
                if ($singlearr['userid'] == $nameid){
                    if(strtotime($zero1) <= strtotime($singlearr['date'])){
                        array_push($finalres,$singlearr); 
                    }
                }
            }
        }
        $all_dates = array_column($finalres,'place');
        array_multisort($all_dates,SORT_ASC,$finalres);
        $all_dates = array_column($finalres,'date');
        array_multisort($all_dates,SORT_ASC,$finalres);
        $this->assign('info',$finalres);
        return $this->fetch('search');
    }
}
