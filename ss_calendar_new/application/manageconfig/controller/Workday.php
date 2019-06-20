<?php
/**
 * 说明该块主要是前端代码比较麻烦，任务主要在前端，
 * 前端代码可在public/static/calendar_redpanda中查看
 * 创建者：向建宇
 * 最新更新：添加日志功能
 */
namespace app\manageconfig\controller;
use think\Model;
use think\Db;
use think\Request;
use app\common\controller\Common;
/*调用同学写的日志模块*/
use app\logmanage\model\Log as LogModel;

class Workday extends Common
{
    public function index(){
        return $this->fetch();
    }

    /*得到存储的日历信息*/
    public function getcalendar(){
        $redata = array();

        //查询数据库，改日历是否已经被记录
        $days=Db::table('workday')->select();

        //返回数据
        // $a = [
        //     'ymd'=> '20190430',
        //     'isWorkDay' => 1
        // ];
        // $b = [
        //     'ymd'=> '20190428',
        //     'isWorkDay' => 0
        // ];

        $redata = [
            'data'=> $days
        ];
        return json($redata);   //不能直接返回数组，否则会报错
    }


    /*存储修改的日历信息*/
    public function savecalendar(){
        //取出传过来的数据
        $param = Request::instance()->param();
        $calendarData = $param['calendarData'];
        //遍历修改的日历信息
        for($i=0;$i<count($calendarData);$i++){
            $a = $calendarData[$i];
            $ymd = substr($a,5,8);
            //$isWorkDay = $a[-2];   //服务器php版本不能识别[-2],不知道为什么
            $isWorkDay = substr($a,-2,1);
            //查询数据库，改日历是否已经被记录
            $isExists	=	Db::table('workday')
              ->where([
                'ymd' =>	['=',$ymd],
              ])->select(); 
            

            if($isExists){
              	$befor_is_work_day = (string)$isExists[0]['is_work_day'];
                //更新数据库
                $is_updata = Db::table('workday')->where([
                    'ymd' =>	['=',$ymd],
                ])->update(['is_work_day' => $isWorkDay]);

                
                //记录到日志里面
                //$model = new LogModel();
                //$uid = ADMIN_ID; // 操作人主键id，非学号
                //$type = 3;
                //$table = 'workday';
                //$field = [
                //$ymd=>[
                //'is_work_day'=> [$befor_is_work_day, $isWorkDay]
                //]
                //];
                //$model->recordLogApi ($uid, $type, $table, $field); //需要判断调用是否成功



            }else{
                //插入数据库
                $res = Db::table('workday')->insert(['ymd'=>$ymd,'is_work_day'=>$isWorkDay]);
                
                //记录到日志里面
                //$model = new LogModel();
                //$uid = ADMIN_ID; // 操作人主键id，非学号
                //$type = 2;
                //$table = 'workday';
                //$field = [$ymd]; // 增加的主键列表，不是学号
                //$model->recordLogApi ($uid, $type, $table, $field); //需要判断调用是否成功

            }

        }

        // $b = '{"ymd":20190430,"name":"廿六","isWorkDay":0}';
        // $b = json_decode($b);
        // var_dump($b);
        // return $b->ymd;
        
        return '修改成功';
    }

}