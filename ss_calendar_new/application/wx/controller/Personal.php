<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 9:43
 */
//备注： 该功能已写到wxcampus/WxCalendar.php下面
namespace app\wx\controller;


use app\wx\common\Common;
use app\logmanage\model\Log as LogModel;
use think\Validate;
use think\Request;
use think\Db;

class PersonalValidator extends Validate
{
    protected $rule =[
        'pagenum' => 'require|number|>=:0',
        'id' => 'require|number|checkTable:schedule_info,id',
        'user_id' => 'require|number',
        'date' => 'require|date|checkDate',
        'time_id' => 'require|number|checkTable:schedule_time,id',
        'place_id' => 'require|number|checkTable:schedule_place,id',
        'item_id' => 'require|number|checkTable:schedule_item,id',
    ];
    protected $scene = [
        'getSchedule' => [
            'pagenum'
        ],
        'create' => [
            'date',
            'time_id',
            'place_id',
            'item_id'
        ],
        'update' => [
            'id',
            'date',
            'time_id',
            'place_id',
            'item_id'
        ],
        'delete' => [
            'id'
        ]
    ];
    protected function checkTable($value, $rule, $data, $field){
        $params = explode(',', $rule);
        return Db::name($params[0])->where($params[1], $value)->count() == 1;
    }
    private function getDdlTimestamp(){
        //TODO
        return time() + 5*24*60*60;
    }
    protected function checkDate($value, $rule, $data, $field){
        $given = strtotime($value);
        $now = strtotime(date('Y-m-d'));
        $ddl = $this->getDdlTimestamp();
        return $now <= $given && $given <= $ddl;
    }
}
class Personal extends Common
{
    protected function getUserId(){
        //TODO
        return 1;
    }
    private function getDdl(){
        //TODO
        return date('Y-m-d', time() + 24*60*60);
    }
    public function getOneDaySchedule($timestamp){
        $page = Db::name('schedule_info')
            ->where('user_id', $this->getUserId())
            ->where('date', date('Y-m-d', $timestamp))
            ->where('is_delete', 0)
            ->select();
        return $page;
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
    public function create(){
        $data = [
            'user_id'       => $this->getUserId(),
            'date'          => input('post.date'),
            'time_id'       => input('post.time_id'),
            'place_id'      => input('post.place_id'),
            'item_id'       => input('post.item_id'),
            'note'          => input('post.note'),
            'is_delete'     => false,
            'create_time'   => time()
        ];
        //检查输入是否有效
        $valid = $this->validate($data, 'app\wx\controller\PersonalValidator.create');
        if($valid !== true){//验证失败
            dump($valid);
            return "failed";
        }
        //插入
        $id = Db::name('schedule_info')->insertGetId($data);
        //记录
        $logRec = new LogModel;
        $logRec->recordLogApi($uid, 2, 0,'schedule_info', $id);
    }
    public function update(){
        $data = [
            'id'            => input('post.id'),
            'user_id'       => $this->getUserId(),
            'date'          => input('post.date'),
            'time_id'       => input('post.time_id'),
            'place_id'      => input('post.place_id'),
            'item_id'       => input('post.item_id'),
            'note'          => input('post.note'),
            'update_time'   => time()
        ];
        $valid = $this->validate($data, 'app\wx\controller\PersonalValidator.update');

        if($valid !== true){//验证失败
            dump($valid);
            return "failed";
        }
        //找到修改了的参数
        $origin = Db::name('schedule_info')
            ->where('id', $data['id'])
            ->where('user_id', $data['user_id'])
            ->find();
        if($origin == NULL){
            return "failed";
        }
        $diff = [];
        foreach($data as $key=>$val){
            if($origin[$key] !== $val){
                $diff[$key] = [$origin[$key], $val];
            }
        }
        // var_dump($diff);
        // return "failed";
        //更新
        $success = Db::name('schedule_info')
            ->where('id', $data['id'])
            ->where('user_id', $data['user_id'])
            ->update($data);
        if($success !== 1){//更新失败
            return "failed";
        }
        //记录日志
        $logRec = new LogModel;
        $logRec->recordLogApi($uid, 3, 0,'schedule_info', [$id => $diff]);
    }

    public function delete($id){
        $uid = getUserId();
        //检查是否有效
        $valid = $this->validate($data, 'app\wx\controller\PersonalValidator.delete');
        if($valid !== true){
            return "failed";
        }
        //删除
        $success = Db::name('schedule_info')
            ->where('id', $id)
            ->where('user_id', $uid)
            ->update([
                'is_delete'     => true,
                'delete_time'   => time()
            ]);
        if($success != 1){//删除失败
            return "failed";
        }
        //记录日志
        $logRec = new LogModel;
        $logRec->recordLogApi($uid, 4, 0,'schedule_info', [$id]);
    }
}