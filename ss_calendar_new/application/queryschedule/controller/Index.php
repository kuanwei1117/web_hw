<?php
namespace app\querySchedule\controller;
use app\common\controller\Common as Common;
use think\Request;
use think\Db; //引入数据库
use think\Input;

class Index extends Common
{
	protected $user_id = 1;

	protected $field_config = array(
		array('name'=>'日期', 'field'=>'date', 'icon'=>'fa-pencil-square-o'),
		array('name'=>'时间', 'field'=>'time_id', 'icon'=>'fa-check-square-o'),
		array('name'=>'事项', 'field'=>'item_id', 'icon'=>'fa-check-square-o'),
		array('name'=>'地点', 'field'=>'place_id', 'icon'=>'fa-check-square-o'),
		array('name'=>'备注', 'field'=>'note', 'icon'=>'fa-pencil-square-o')
	);

	public function index()
	{
		$this->assign('schedule_info', $this->defaultList());
		$this->assign('fields', $this->field_config);
		return $this->fetch();
	}
	public function query(Request $request)
	{
		$starttime = $request->param('starttime');
		$endtime = $request->param('endtime');
		//$starttime = '2019-04-10';
		//$endtime = '2019-05-01';
		$sql = "select * from schedule_info where date(date) between date('".$starttime."') and date('".$endtime."')";
		$result = Db::query($sql);  
		$len = count($result);
		for($x = 0; $x < $len; $x++){
			$place_id = $result[$x]['place_id'];
			$location = Db::table('schedule_place')->where('id', $place_id)->value('name');
			$result[$x]['location'] = $location;
			$item_id = $result[$x]['item_id'];
			$event = Db::table('schedule_item')->where('id', $item_id)->value('name');
			$result[$x]['event'] = $event;
		}
		$this->assign('schedule_info', $result);
		return $this->fetch('result');
	}
	
	public function defaultList(){
		$sql = "select * from schedule_info where user_id = ".$this->user_id." and is_delete = false";
		$result = Db::query($sql);
		return $result;
	}
	
	public function postTest(){
		$fields = Request::instance()->post();
		var_dump($fields);
	}

	public function postSchedule(){
		$post = Request::instance()->post();
		
	}
}