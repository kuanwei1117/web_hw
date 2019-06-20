<?php
/**
 * Date: 2019/6/18
 * Time: 22:13
 * Author: yang kang
 */

namespace app\querystatistics\controller;
use app\common\controller\Common;
use think\Session;

use app\querystatistics\model\ScheduleSearch as ScheduleModel;
use think\controller;
use think\Db;
use think\Request;

class Query extends Common{

    public function index(){
        //默认查询上周、本周、下周日程
        $schedul_model = new ScheduleModel();
        $info1 = $schedul_model->searchThreeWeekInfo();
        $now=date('Y-m-d',time());  //查询当前日期
        $this_week_number = date('W',strtotime($now)); //查询本周位于今年第多少周

        for($i = 0;$i < sizeof($info1);$i++) {
            if ($info1[$i]['week_number'] < $this_week_number) {
                switch ($info1[$i]['week']) {
                    case 0:
                        $info1[$i]['week'] = '上周日';
                        break;
                    case 1:
                        $info1[$i]['week'] = '上周一';
                        break;
                    case 2:
                        $info1[$i]['week'] = '上周二';
                        break;
                    case 3:
                        $info1[$i]['week'] = '上周三';
                        break;
                    case 4:
                        $info1[$i]['week'] = '上周四';
                        break;
                    case 5:
                        $info1[$i]['week'] = '上周五';
                        break;
                    case 6:
                        $info1[$i]['week'] = '上周六';
                        break;
                }
            } elseif ($info1[$i]['week_number'] == $this_week_number) {
                switch ($info1[$i]['week']) {
                    case 0:
                        $info1[$i]['week'] = '本周日';
                        break;
                    case 1:
                        $info1[$i]['week'] = '本周一';
                        break;
                    case 2:
                        $info1[$i]['week'] = '本周二';
                        break;
                    case 3:
                        $info1[$i]['week'] = '本周三';
                        break;
                    case 4:
                        $info1[$i]['week'] = '本周四';
                        break;
                    case 5:
                        $info1[$i]['week'] = '本周五';
                        break;
                    case 6:
                        $info1[$i]['week'] = '本周六';
                        break;
                }
            } else {
                switch ($info1[$i]['week']) {
                    case 0:
                        $info1[$i]['week'] = '下周日';
                        break;
                    case 1:
                        $info1[$i]['week'] = '下周一';
                        break;
                    case 2:
                        $info1[$i]['week'] = '下周二';
                        break;
                    case 3:
                        $info1[$i]['week'] = '下周三';
                        break;
                    case 4:
                        $info1[$i]['week'] = '下周四';
                        break;
                    case 5:
                        $info1[$i]['week'] = '下周五';
                        break;
                    case 6:
                        $info1[$i]['week'] = '下周六';
                        break;
                }
            }
        }
        $this->assign('info',$info1);
        return $this->fetch("index");
	}

    public function searchTime($s_time,$t_time){
        //查询特定时间段日程

        //$start_time = Request::instance()->param('s_time');
        //$terminal_time = Request::instance()->param('t_time');
        //echo "<script>console.log($start_time)</script>";

        $schedul_model = new ScheduleModel();
        $info1 = $schedul_model->searchPartialInfo($s_time,$t_time);
        $this->assign('info',$info1);
        return $this->fetch("searchTime");
        //$this->success('获取成功', '', $this->fetch('searchTime'));
    }

    public function searchAllSchedule(){
        //查询所有日程
        $schedul_model = new ScheduleModel();
        $info = $schedul_model->searchAllInfo();
        $this->assign('info',$info);
        return $this->fetch("searchAllSchedule");
    }
}

