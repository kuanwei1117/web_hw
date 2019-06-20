<?php
/*
 * 查询用户日程
 * 第二组小刘
 * 2019-5-15
 */

namespace app\wxcampus\controller;
use app\common\controller\Common;
use think\Controller;
use think\Db;
use think\Request;


class Wxquery extends controller
{
  public function Index($userid, $wxcode, $number)
  {
    $this->userid = $userid;
    $this->wxcode = $wxcode;
    $this->number = $number;

    
    // 检查查询的用户名是否存在
    $all_name = Db::table('user_info')
        ->where('is_delete', 0)
        ->field('name')
        ->select();
    $allname = array();
    foreach($all_name as $k=>$v){
    	$allname[] = $v['name'];
    }
	$allnameall = array(
        'name' => $allname
    );

    // 检查是否在白名单列
    $name_list = Db::table('user_info')
    	->join('white_list', 'white_list.user_id = user_info.id')
        ->where('white_list.is_delete', 0)
        ->where('user_info.is_delete', 0)
        ->field('user_info.id, user_info.name')
        ->select();
    $list = array();
    foreach($name_list as $k=>$v){
    	$list[] = $v;
    }
    // 转换成typeahead需要的json格式数据
    $res = json_encode($list, JSON_UNESCAPED_UNICODE);  //array to json
    
    

    $this->assign('allname', $allnameall);
    $this->assign('userid', $userid);
    $this->assign('wxcode' ,$wxcode);
    $this->assign('number' ,$number);
   	$this->assign('name_list',$res);

    return $this->fetch('index/wx_search');
  }
  
  
  
  
  public function search()
  {
    //按照 姓名 模糊查询领导日程
      
    $query_name = '';
    
    if(input('?get.name')){
        $query_name = Request::instance()->param('name','','strip_tags,htmlspecialchars');
    }
    
    // 检查用户名是否存在
    $all_name = Db::table('user_info')
        ->where('is_delete', 0)
        ->field('name')
        ->select();
    $allname = array();
    foreach($all_name as $k=>$v){
    	$allname[] = $v['name'];
    }

    
    // 检查是否在白名单列
    $white_list = Db::table('user_info')
    	->join('white_list', 'white_list.user_id = user_info.id')
        ->where('white_list.is_delete', 0)
        ->where('user_info.is_delete', 0)
        ->field('user_info.name')
        ->select();
    $whitelist = array();
    foreach($white_list as $k=>$v){
    	$whitelist[] = $v['name'];
    }

	// 取出白名单中的user_id，与user_info表中的id对应，获得user_info表中的name，与用户查询的name比较
    $usr_info = Db::table('user_info')
    	->join('white_list', 'white_list.user_id = user_info.id')
      	->join('user_depart', 'user_info.depart_id = user_depart.id')
        ->join('user_position', 'user_info.position_id = user_position.id')
    	->where('white_list.is_delete', 0)
    	->where('user_info.is_delete', 0)
    	->field('user_info.name as name, user_depart.name as depart, user_position.name as position')
    	->select();

    $namelist = array();
    $depart = array();
    $position = array();

    foreach($usr_info as $k=>$v){
    	$namelist[] = $v['name'];
      	$depart[] = $v['depart'];
      	$position[] = $v['position'];
    }
    
    // 根据查询的领导姓名，调出该领导的其他部门、职位信息，得到query_info
    
    // dump($whitelist);
    
    
    // 判断用户输入的查询姓名是否存在、是否在白名单中，若在白名单中是否有日程信息，则查询数据库，调出该用户的日程信息
    
    if (!in_array($query_name, $allname)){
    	// echo '该用户不存在';
      	$this->assign('query', $query_name);
      	$this->assign('result', 'noexist');
    }
    elseif (!in_array($query_name, $whitelist)){
        // echo '用户不在白名单上'; 
      	$this->assign('query', $query_name);
        $this->assign('result', 'notwhite');
    }
    elseif (!in_array($query_name, $namelist)){
        // echo '是白名单上的普通用户'; 
      	$this->assign('query', $query_name);
        $this->assign('result', 'nope');
    }else{ 
        $key = array_search($query_name, array_column($usr_info, 'name'));
    	$depart = $usr_info[$key]['depart'];
      	$position = $usr_info[$key]['position'];
    
    	$query_info = array(
        	'name' => $query_name,
      		'depart' => $depart,
      		'position' => $position
        );
        
		global $sche_info;
        $sche_info = Db::table('schedule_info')
          // ->alias(['schedule_info' => 'a', 'user_info' => 'b', 'user_position' => 'c', 'schedule_time' => 'd', 'schedule_place' => 'e', 'schedule_item' => 'f'])
          ->join('user_info', 'user_info.id = schedule_info.user_id')
          ->join('schedule_time', 'schedule_info.time_id = schedule_time.id')
          ->join('schedule_place', 'schedule_info.place_id = schedule_place.id')
          ->join('schedule_item', 'schedule_info.item_id = schedule_item.id')
          ->where('schedule_info.is_delete', 0)
          ->where('user_info.is_delete', 0)
          ->where('user_info.name', $query_name)
          ->where('schedule_info.date', '>= time', date('Y-m-d', time()))
          ->field('schedule_info.date as date, schedule_time.name as time,schedule_time.time_order as time_order, schedule_place.name as place, schedule_item.name as item')
          ->order('date, time_order, time')
          ->select();
      
      	if (!is_array($sche_info)){
          	// echo '暂无日程安排';
          	$this->assign('query', $query_info);
        	$this->assign('result', NULL);
        }else{
          $this->assign('query', $query_info);
          $this->assign('result', $sche_info);
          //dump($sche_info);
        }
    }

    return $this->fetch('index/wx_searchlist');

  }
  
}
