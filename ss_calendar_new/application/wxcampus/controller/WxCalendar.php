<?php

// Copyright (c) 2019 Hu Yingcong.
// Copyright (c) 2019 Tan Cong.
// Copyright (c) 2019 Yang Changhe.

namespace app\wxcampus\controller;

use think\Controller;
use app\logmanage\model\Log as LogModel;
use think\Validate;
use think\Request;
use think\Db;

//描述：用户更新自己的日程
//1.用户通过点击日程选项，会显示自己当天的日程；
//2.用户通过点击加号按钮，跳转到新建日程页面；
//3.用户点击当天的日程，跳转到修改日程页面；
//4.用户点击上面的两个箭头，跳转到前天和明天的日程页面， 从而修改其他时间的日程
//5.用户点击日历可以进行跳转

//TODO list:
// 1. 白名单相关检查 (done)
// 2. 工作日的相关检查 (有啥好做的need more specified)(done)
// 3. 删除按钮的添加 (咋做？)
// 4. 日历的跳转 (done)
// 5. 没有权限时的跳转页面 (done)
// 6. 默认日程的相关工作（让默认日程的同学自己加到数据库表里）
//2019.6.13更新：
// 7. 用户日期栏需要显示一个时间范围 (杨昌和done)
// 8. 用户可以点选日程，添加按钮的日程需要根据用户选中的日程进行添加 (杨昌和done)
// 9. 日程显示一整周的日程，红色栏显示日期，底下显示当天日程 (done)
// 10. 今天之前的日期不显示在一整周的日程内 (done)
// 更新：之前的日程可以显示，但是不能修改，已经修改了样式和链接
// 11. 今天的日期高亮显示 (done)

//Refactor list:
// 1. 把页面放到view/WxCalendar下 (done)
// 2. 删除wxcode () (done)
// 3. 参数用在input助手函数获取 (done)

//bug list:
//1. 导航栏还没做好(Index默认要传wxcode, 很麻烦) 
//更新： 传入wxcode也没用, 还是提示error        (基本做好)
//2. 新增日程默认的日程是当天， 即使页面是其他天 (done)
//3. 更改页面的选项没有随着用户的选中的事项来改变 (done)
//4. 新增时数据库里create_time未被修改,而是null (done)
//5. 修改日程时create_time被修改，但update_time未被修改，而是null
// 更新：修改日程时会修改update_time,但是create_time也会变 
//6. 可以修改过去的日程 (need more specified)
//7. 可以修改期限以外的日程 (done)
//8. 点击新增日程没反应，但成功添加，没有跳转；(fixed)
//9. iOS客户端新增日程时必须先拉一下才能选中


//for developer:
//1. 入口在Index(); 
//2. 新增，更新日程页面在CreatePage(), UpdatePage()；以上方法都需要传参数(虽然wxcode是不必要的)
//3. 新增，更新日程，利用了接口Create, Update;
//4. 前端代码在view/index/wx_detail.html和 wiew/index/wx_calender.html
//5. 导航栏写在view/index/nav.html

class CalendarValidator extends Validate
{
    protected $rule =[
        'id' => 'require|number|checkTable:schedule_info,id',
        'user_id' => 'require|number|checkWhiteList',
        'date' => 'require|date|checkWorkDay',
        'time_id' => 'require|number|checkTable:schedule_time,id',
        'place_id' => 'require|number|checkTable:schedule_place,id',
        'item_id' => 'require|number|checkTable:schedule_item,id',
        'note' => 'checkLength'
    ];
    protected $scene = [
        'create' => [
            'date' => 'require|date|checkPastDdl|checkFutureDdl|checkWorkDay',
            'time_id',
            'place_id',
            'item_id',
            'user_id'
        ],
        'update' => [
            'id',
            'user_id',
            'date' => 'require|date|checkFutureDdl|checkWorkDay',
            'time_id',
            'place_id',
            'item_id',
            'user_id'
        ],
        'delete' => [
            'id',
            'user_id'
        ],
        'index' => [
            'user_id'
        ]
    ];
    protected $message = [
        'time_id.require' => '时间不能为空！',
        'place_id.require' => '地点不能为空！',
        'item_id.require' => '事项不能为空！',
        'id.checkTable' => '日程ID无效',
        'time_id' => '时间ID无效',
        'place_id' => '地点ID无效',
        'item_id' => '事项ID无效',
        'date.checkPastDdl' => '不可以创建当天之前的日程！',
        'date.checkFutureDdl' => '当前日期超过可维护范围！',
        'date.checkWorkDay' => '当前日期不是工作日，不可添加或修改！',
        'user_id.checkWhiteList' => '当前用户不在白名单上！'
    ];
    protected function checkTable($value, $rule, $data, $field){
        $params = explode(',', $rule);
        return Db::name($params[0])->where($params[1], $value)->count() == 1;
    }

    protected function checkPastDdl($value, $rule, $data, $field){
        $given = strtotime($value);
        $now = strtotime(date('Y-m-d'));
        //没有过去日期的维护范围，那么都可以修改
        return $now <= $given;
    }
    protected function checkFutureDdl($value, $rule, $data, $field){
        $given = strtotime($value);
        $now = strtotime(date('Y-m-d'));
        //从0点算起的可维护范围
        $scopeRes = DB::table('global_config')->where('config_item', 'scope')->find();
        if($scopeRes){
            $scopeRes = $scopeRes['parameter'];
        }else{
            return false;
        }
        return $given <= $now + $scopeRes;
    }
    protected function checkWorkDay($value, $rule, $data, $field){
        $given = date('Ymd', strtotime($value));
        //查看是不是工作日
        // var_dump($given);
        $res = DB::table('workday')->where('ymd', $given)->find();
        if($res){
            $res = $res['is_work_day'];
            return $res != 0; // 为0时返回错误
        }
        return true; //其他情况返回正确
    }
    protected function checkWhiteList($value, $rule, $data, $field){
        $res = DB::table('white_list')
            ->where('user_id', $value)
            ->where('is_delete', 0)
            ->find();
        return $res != NULL;
    }
}
// uuid: 5pS55L2g5aaI5ZGi5pS577yM6Ieq5bex55qE6aG555uu5ou/57uZ5a2m55Sf5YGa77yM5aW95oSP5oCd77yf
class WxCalendar extends Controller
{
    //apis
    private $uid;
    private $weekdayName = [
        '星期天',
        '星期一',
        '星期二',
        '星期三',
        '星期四',
        '星期五',
        '星期六'
    ];
    protected function getUserId(){
        return $this->uid;
    }
    protected function getOneDaySchedule($timestamp){
        $page = Db::name('schedule_info')
            ->where('user_id', $this->getUserId())
            ->where('date', date('Y-m-d', $timestamp))
            ->where('is_delete', 0)
            ->select();
        return $page;
    }
    protected function getSchedule($userid, $scheduleId){
        return Db::name('schedule_info')
            ->where('user_id', $userid)
            ->where('id', $scheduleId)
            ->find();
    }
    //返回所有相关字段, 保证当一个项被删除后, 依然可以显示.
    protected function getAllScheduleItems(){
        return Db::name('schedule_item')
            ->select();
    }
    protected function getAllSchedulePlaces(){
        return Db::name('schedule_place')
            ->select();
    }
    protected function getAllScheduleTimes(){
        return Db::name('schedule_time')
            ->select();
    }
    protected function getScheduleItems(){
        return Db::name('schedule_item')
        ->where('is_delete', 0)
        ->select();
    }
    protected function getSchedulePlaces(){
        return Db::name('schedule_place')
        ->where('is_delete', 0)
        ->select();
    }
    protected function getScheduleTimes(){
        return Db::name('schedule_time')
        ->where('is_delete', 0)
        ->select();
    }
    protected function json($method, $success, $message){
        return json([
            'method' => $method,
            'success'=> $success,
            'message'=> $message
        ]);
    }

    public function create(){
        $userid = input('param.userid');
        $data = [
            'user_id'       => $userid,
            'date'          => input('post.date'),
            'time_id'       => input('post.time_id'),
            'place_id'      => input('post.place_id'),
            'item_id'       => input('post.item_id'),
            'note'          => input('post.note'),
            'create_time'   => date('Y-m-d H:i:s')
        ];
        //检查输入是否有效
        $valid = $this->validate($data, 'app\wxcampus\controller\CalendarValidator.create');
        if($valid !== true){//验证失败
            return $this->json('create', false, $valid);
        }
        //插入
        $id = Db::name('schedule_info')->insertGetId($data);
        //记录
        $logRec = new LogModel;
        ob_start();
        $logRec->recordLogApi($userid, 2, 0,'schedule_info', [$id]);
        ob_end_clean();//清除打印内容
        return $this->json('create', true, 'success');
    }
    public function update(){
        $userid = input('param.userid');
        $data = [
            'id'            => input('post.id'),
            'user_id'       => $userid,
            'date'          => input('post.date'),
            'time_id'       => input('post.time_id'),
            'place_id'      => input('post.place_id'),
            'item_id'       => input('post.item_id'),
            'note'          => input('post.note'),
            'update_time'   => date('Y-m-d H:i:s')
        ];
        $valid = $this->validate($data, 'app\wxcampus\controller\CalendarValidator.update');
        
        if($valid !== true){//验证失败
            return $this->json('update', false, $valid);;
        }
        //找到修改了的参数
        $origin = Db::name('schedule_info')
            ->where('id', $data['id'])
            ->where('user_id', $data['user_id'])
            ->find();
        if($origin == NULL){
            return $this->json('update', false, '找不到要修改的参数');;
        }
        $diff = [];
        foreach($data as $key=>$val){
            if($origin[$key] !== $val){
                $diff[$key] = [$origin[$key], $val];
            }
        }
        //更新
        $success = Db::name('schedule_info')
            ->where('id', $data['id'])
            ->where('user_id', $data['user_id'])
            ->update($data);
        if($success !== 1){//更新失败
            return $this->json('update', false, '数据库插入失败!');
        }
        //记录日志
        $logRec = new LogModel;
        ob_start();
        $logRec->recordLogApi($userid, 3, 0,'schedule_info', [$data['id'] => $diff]);
        ob_end_clean();//清除打印内容
        return $this->json('update', true, 'success');
    }

    public function delete(){
        $userid = input('post.userid');
        $id = input('post.id');
        $data = [
            'user_id' => $userid,
            'id' => $id
        ];
        //检查是否有效
        $valid = $this->validate($data, 'app\wxcampus\controller\CalendarValidator.delete');
        if($valid !== true){
            return $this->json('delete', false, $valid);
        }
        //删除
        $success = Db::name('schedule_info')
            ->where('id', $id)
            ->where('user_id', $userid)
            ->update([
                'is_delete'     => 1,
                'delete_time'   => date('Y-m-d H:i:s')
            ]);
        if($success != 1){//删除失败
            return $this->json('delete', false, '数据库删除失败!');
        }
        //记录日志
        $logRec = new LogModel;
        ob_start();
        $logRec->recordLogApi($userid, 4, 0,'schedule_info', [$id]);
        ob_end_clean();
        return $this->json('delete', true, 'success');
    }
    //Views
    protected $items;
    protected $places;
    protected $times;
    protected $date_now;
    public function Index(){
        //ZnVjayB6aGFuZ3F4 
        $date_now = date('Y-m-d');
        $code = input('param.wxcode');
        $userid = input('param.userid');
        $date = input('param.date');
        $isInTheWhiteList = $this->validate(['user_id' => $userid], 'app\wxcampus\controller\CalendarValidator.index');
        if($isInTheWhiteList !== true){
            return $this->error('您不在白名单上，请联系管理员');
        }
        $this->uid = $userid;
        if($date == NULL)$date = date('Y-m-d');
        $this->items = $this->getAllScheduleItems();
        $this->places = $this->getAllSchedulePlaces();
        $this->times = $this->getAllScheduleTimes();
        $this->assign([
            'wxcode' => 1,
            'number' => $userid,
            'userid' => $userid,
            'date'   => date('Y-m-d',strtotime($date)),
            'cells'  => $this->getScheduleDisplayArray(strtotime($date)),
            'left'   => url('index', ['userid'=>$userid, 'date'=> date('Y-m-d',strtotime($date)-7*24*60*60)]),
            'right'  => url('index', ['userid'=>$userid, 'date'=> date('Y-m-d',strtotime($date)+7*24*60*60)]),
            'date_now' => $date_now
        ]);
        return $this->fetch("index");
    }
    protected function getScheduleDisplayArray($timestamp){
        assert($this->items != NULL);
        assert($this->places!= NULL);
        assert($this->times != NULL);
        $cells = [];
        $date = date('Y-m-d', $timestamp);//得到给定的日期
        $leftDate = date('Y-m-d', strtotime('-1 sunday', $timestamp+24*60*60));
        //这周日
        $rightDate = date('Y-m-d', strtotime('saturday', $timestamp));//这周六
        $curTimestamp = strtotime(date('Y-m-d'));
        $leftTimestamp = strtotime($leftDate);
        $rightTimestamp = strtotime($rightDate);
        for($tmpTimestamp = $leftTimestamp; $tmpTimestamp <= $rightTimestamp; $tmpTimestamp += 24*60*60){
            $schedules = $this->getOneDaySchedule($tmpTimestamp);
            if(count($schedules) == 0){
                continue;
            }
            $date = date('Y-m-d', $tmpTimestamp);
            $dateClass = $tmpTimestamp < $curTimestamp ? "date-title-past" : "";
            $dateClass = $tmpTimestamp == $curTimestamp ? "date-title-today" : $dateClass; 
            $cell = [
                'date' => $date." ".$this->weekdayName[date('w',$tmpTimestamp)],
                'class' => $dateClass,
                'data' => [] 
            ];
            $cells[$date] = $cell;
            foreach ($schedules as $sched){
                $timeid = $sched['time_id'];
                $itemid = $sched['item_id'];
                $placeid = $sched['place_id'];
                $timename = '';
                $itemname = '';
                $placename = '';
                foreach($this->times as $timeunit) {
                    if($timeunit['id'] == $timeid){
                        $timename = $timeunit['name'];
                        break;
                    }
                }
                foreach($this->items as $itemunit) {
                    if($itemunit['id'] == $itemid){
                        $itemname = $itemunit['name'];
                        break;
                    }
                }
                foreach($this->places as $placeunit) {
                    if($placeunit['id'] == $placeid){
                        $placename = $placeunit['name'];
                        break;
                    }
                }
                $emptyUrl = "javascript:void(0)";
                $updateUrl = url('updatePage', ['userid' => $this->getUserId(),'id'=> $sched['id']]);
                $updateUrl = $tmpTimestamp < $curTimestamp ? $emptyUrl : $updateUrl; //过去的日程不能更新
                $class = $tmpTimestamp < $curTimestamp ? "past-schedule" : "";
                $dataItem = [
                    'time' => $timename,
                    'item' => $itemname,
                    'note' => $sched['note'],
                    'place'=> $placename,
                    'id'   => $sched['id'],
                    'url'  => $updateUrl,
                    'class' => $class
                ];
                array_push($cells[$date]['data'], $dataItem);
            }
        }
        return $cells;
    }
    protected function detail(){
        // Config::Set('url_common_param', true)
        $this->assign([
            'maxlength' => 200
        ]);
        return $this->fetch("detail");
    }
    public function updatePage(){
        $userid = input('param.userid'); 
        $id = input('param.id');
        $sched = $this->getSchedule($userid, $id);
        $this->assign([
            'userid' =>  $userid ,
            'scheduleid' =>  $id ,
            'date' =>  $sched['date'] ,
            'note' =>  $sched['note'] ,
            'time_id' => $sched['time_id'],
            'place_id' => $sched['place_id'],
            'item_id' => $sched['item_id'],
            'title' =>  '修改日程' ,
            'confirmid' =>  'update-btn',
            'items' => $this->getScheduleItems(),
            'times' => $this->getScheduleTimes(),
            'places'=> $this->getSchedulePlaces(),
            'initstatus' => ''
        ]);
        return $this->detail();
    }
    public function createPage(){
        $userid = input('param.userid');
        $date   = input('param.date');
        if($date === NULL){
            $date = date('Y-m-d');
        }else{
            $date = date('Y-m-d', strtotime($date));
        }
        $items = $this->getScheduleItems();
        $times = $this->getScheduleTimes();
        $places = $this->getSchedulePlaces();
        $this->assign([
            'userid' =>  $userid ,
            'scheduleid' =>  -1 ,
            'date' =>  $date ,
            'note' =>  '' ,
            'title' =>  '添加日程' ,
            'time_id' => $times[0]['id'],//默认值？buggy here
            'place_id' => $places[0]['id'],
            'item_id' => $items[0]['id'],
            'items' => $items,
            'times' => $times,
            'places'=> $places,
            'confirmid' =>  'create-btn',
            'initstatus' => '请选择时间，地点和事项类型'
        ]);
        return $this->detail();
    }
}