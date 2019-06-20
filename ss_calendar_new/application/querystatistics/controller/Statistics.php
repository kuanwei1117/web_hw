<?php
namespace app\querystatistics\controller;

use app\common\controller\Common;
use think\Controller;
use think\Request;
use think\Db; 

/**
*	@	Purpose:
*	 统计用户日程信息的类
*	@Author:	刘博楠
*	@Date:	2019/4/17
*	@Time:	20:00
*/

class Statistics extends Common
{
	
		/**
		*	@Purpose:
		*	 执行一次查询
		*	@Method	Name:	index()
		*
		*	@Author:	刘博楠
		*
		*	@Return:	查询返回值（结果集对象）
		*/
    public function index()
    {
				$list = DB::query("SELECT distinct user_info.id id,user_info.name name,user_depart.name depart_name,user_position.name user_posname 
									FROM user_info,schedule_info,user_depart,user_position 
									where user_info.id=schedule_info.user_id and 
									user_info.is_delete=0 and 
									schedule_info.is_delete=0 and 
									user_info.depart_id=user_depart.id and 
									user_info.position_id=user_position.id and 
									user_depart.is_delete=0 and 
									user_position.is_delete=0");
				$this->assign('arealist', $list);
				return $this->fetch('index');
	}

  	public function placeInfoWithTime(){
		$id=$_POST['id'];
		$start=$_POST['start'];
		$end=$_POST['end'];
		$sql="SELECT count(schedule_info.place_id) count,schedule_place.name place FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
			where schedule_info.time_id=schedule_time.id and 
			schedule_info.item_id=schedule_item.id 
			and schedule_info.place_id=schedule_place.id and 
			schedule_info.user_id=user_info.id and 
			schedule_info.is_delete=0 and 
			schedule_item.is_delete=0 and 
			schedule_place.is_delete=0 and 
			schedule_time.is_delete=0 and 
			schedule_info.user_id=".$id." 
			and schedule_info.date BETWEEN '".$start."' and '".$end."'  group by place_id";
			$list = DB::query($sql);
				//return json_encode($list, JSON_UNESCAPED_UNICODE);
				return $list;
	}
  
	public function placeInfoAllTime(){
		$id=$_POST['id'];
		$list = DB::query("SELECT count(schedule_info.place_id) count,schedule_place.name place FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
							where schedule_info.time_id=schedule_time.id and 
							schedule_info.item_id=schedule_item.id 
							and schedule_info.place_id=schedule_place.id and 
							schedule_info.user_id=user_info.id and 
							schedule_info.is_delete=0 and 
							schedule_item.is_delete=0 and 
							schedule_place.is_delete=0 and 
							schedule_time.is_delete=0 and 
							schedule_info.user_id=".$id." 
							group by place_id");
				//return json_encode($list, JSON_UNESCAPED_UNICODE);
				return $list;
	}

	public function placeInfoOne(){
		$id=$_POST['id'];
		$list = DB::query("SELECT count(schedule_info.place_id) count,schedule_place.name place FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
							where schedule_info.time_id=schedule_time.id and 
							schedule_info.item_id=schedule_item.id 
							and schedule_info.place_id=schedule_place.id and 
							schedule_info.user_id=user_info.id and 
							schedule_info.is_delete=0 and 
							schedule_item.is_delete=0 and 
							schedule_place.is_delete=0 and 
							schedule_time.is_delete=0 and schedule_info.date>DATE_SUB(CURDATE(), INTERVAL 1 YEAR) and
							schedule_info.user_id=".$id." 
							group by place_id");
				//return json_encode($list, JSON_UNESCAPED_UNICODE);
				return $list;
	}

	public function placeInfoHalf(){
		$id=$_POST['id'];
		$list = DB::query("SELECT count(schedule_info.place_id) count,schedule_place.name place FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
							where schedule_info.time_id=schedule_time.id and 
							schedule_info.item_id=schedule_item.id 
							and schedule_info.place_id=schedule_place.id and 
							schedule_info.user_id=user_info.id and 
							schedule_info.is_delete=0 and 
							schedule_item.is_delete=0 and 
							schedule_place.is_delete=0 and 
							schedule_time.is_delete=0 and schedule_info.date>DATE_SUB(CURDATE(), INTERVAL 6 MONTH) and
							schedule_info.user_id=".$id." 
							group by place_id");
				//return json_encode($list, JSON_UNESCAPED_UNICODE);
				return $list;
	}

	public function placeInfoWeek(){
		$id=$_POST['id'];
		$list = DB::query("SELECT count(schedule_info.place_id) count,schedule_place.name place FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
							where schedule_info.time_id=schedule_time.id and 
							schedule_info.item_id=schedule_item.id 
							and schedule_info.place_id=schedule_place.id and 
							schedule_info.user_id=user_info.id and 
							schedule_info.is_delete=0 and 
							schedule_item.is_delete=0 and 
							schedule_place.is_delete=0 and 
							schedule_time.is_delete=0 and schedule_info.date>DATE_SUB(CURDATE(), INTERVAL 1 WEEK) and
							schedule_info.user_id=".$id." 
							group by place_id");
				//return json_encode($list, JSON_UNESCAPED_UNICODE);
				return $list;
	}

  	public function itemInfoWithTime(){
		$id=$_POST['id'];
		$start=$_POST['start'];
		$end=$_POST['end'];
		$list = DB::query("SELECT count(schedule_info.item_id) count,schedule_item.name item FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
							where schedule_info.time_id=schedule_time.id and 
							schedule_info.item_id=schedule_item.id 
							and schedule_info.place_id=schedule_place.id and 
							schedule_info.user_id=user_info.id and 
							schedule_info.is_delete=0 and 
							schedule_item.is_delete=0 and 
							schedule_place.is_delete=0 and 
							schedule_time.is_delete=0 and 
							schedule_info.user_id=".$id." 
							and schedule_info.date BETWEEN '".$start."' and '".$end."' group by schedule_item.name");
				return $list;
	}
  
	public function itemInfoAllTime(){
		$id=$_POST['id'];
		$list = DB::query("SELECT count(schedule_info.item_id) count,schedule_item.name item FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
							where schedule_info.time_id=schedule_time.id and 
							schedule_info.item_id=schedule_item.id 
							and schedule_info.place_id=schedule_place.id and 
							schedule_info.user_id=user_info.id and 
							schedule_info.is_delete=0 and 
							schedule_item.is_delete=0 and 
							schedule_place.is_delete=0 and 
							schedule_time.is_delete=0 and 
							schedule_info.user_id=".$id." 
							group by schedule_item.name");
				return $list;
	}

	public function itemInfoOne(){
		$id=$_POST['id'];
		$list = DB::query("SELECT count(schedule_info.item_id) count,schedule_item.name item FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
							where schedule_info.time_id=schedule_time.id and 
							schedule_info.item_id=schedule_item.id 
							and schedule_info.place_id=schedule_place.id and 
							schedule_info.user_id=user_info.id and 
							schedule_info.is_delete=0 and 
							schedule_item.is_delete=0 and 
							schedule_place.is_delete=0 and 
							schedule_time.is_delete=0 and schedule_info.date>DATE_SUB(CURDATE(), INTERVAL 1 YEAR) and
							schedule_info.user_id=".$id." 
							group by schedule_item.name");
				return $list;
	}

	public function itemInfoHalf(){
		$id=$_POST['id'];
		$list = DB::query("SELECT count(schedule_info.item_id) count,schedule_item.name item FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
							where schedule_info.time_id=schedule_time.id and 
							schedule_info.item_id=schedule_item.id 
							and schedule_info.place_id=schedule_place.id and 
							schedule_info.user_id=user_info.id and 
							schedule_info.is_delete=0 and 
							schedule_item.is_delete=0 and 
							schedule_place.is_delete=0 and 
							schedule_time.is_delete=0 and schedule_info.date>DATE_SUB(CURDATE(), INTERVAL 6 MONTH) and
							schedule_info.user_id=".$id." 
							group by schedule_item.name");
				return $list;
	}

	public function itemInfoWeek(){
		$id=$_POST['id'];
		$list = DB::query("SELECT count(schedule_info.item_id) count,schedule_item.name item FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
							where schedule_info.time_id=schedule_time.id and 
							schedule_info.item_id=schedule_item.id 
							and schedule_info.place_id=schedule_place.id and 
							schedule_info.user_id=user_info.id and 
							schedule_info.is_delete=0 and 
							schedule_item.is_delete=0 and 
							schedule_place.is_delete=0 and 
							schedule_time.is_delete=0 and schedule_info.date>DATE_SUB(CURDATE(), INTERVAL 1 WEEK) and
							schedule_info.user_id=".$id." 
							group by schedule_item.name");
				return $list;
	}


		/**
		*	@Purpose:
		*	 执行一次查询
		*	@Method	Name:	statisticsFun()
		*
		*	@Author:	刘博楠
		*
		*	@Return:	查询返回值（结果集对象）
		*/
    public function statisticsFun(){
				$id=$_GET['id'];
				$start = request()->post("start");
        		$end = request()->post("end");
				$list = DB::query("SELECT user_info.id id, user_info.name name,schedule_info.date date,schedule_time.name time,schedule_place.name place,schedule_item.name item 
													FROM schedule_info,schedule_item,schedule_place,schedule_time,user_info 
													where schedule_info.time_id=schedule_time.id and 
													schedule_info.item_id=schedule_item.id 
													and schedule_info.place_id=schedule_place.id and 
													schedule_info.user_id=user_info.id and 
													schedule_info.is_delete=0 and 
													schedule_item.is_delete=0 and 
													schedule_place.is_delete=0 and 
													schedule_time.is_delete=0 and 
													schedule_info.user_id=".$id." order by schedule_info.date desc");
				if(count($list)>0){
					$this->assign('arealist', $list);
					return $this->fetch('info');
				}
				else{
					//dump(count($list));
					return $this->fetch('index');
				}		
	}
}

