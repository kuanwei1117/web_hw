<?php
namespace app\manageconfig\model;
use think\Model;
use think\Db;

/**
 * @Purpose:
 * schedule_time 数据表交互，增删改用户可选时间，如上午、下午等
 * @Author 第12组 黄捷
 * @Date 2019-4-18
 * @Time 8：37 
 */
class ScheduleTime extends Model
{
	protected $table = 'schedule_time';
	protected $autoWriteTimestamp = 'datetime';
	protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

	// 获取所有已注册时间段
	public function getAllTime(){
		// is_delete标记为零  
		$items = new ScheduleTime();
		$res = $items->where('is_delete',0)->order('time_order')->select();
		return $res;
	}

}