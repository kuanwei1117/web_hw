<?php
namespace app\querystatistics\controller;

use app\common\controller\Common;
use think\Controller;
use think\Request;
use think\Db; 

/**
*	@	Purpose:
*	 统计用户日程信息的类
*	@Author:	邓凌鹏
*	@Date:	2019/4/24
*	@Time:	19:00
*/

class UserNotUpdate extends Common
{
		
  		/**
		*	@Purpose:
		*	 查询未更新日程的用户
		*	@Method	Name:	index()
		*
		*	@Author:	邓凌鹏
		*
		*	@Return:	查询返回值（结果集对象）
		*/
  	public function index()
    {
				$today = date("Y-m-d");
      			$next_week = date("Y-m-d",time()+3600*24*7);
      /*
       			$sql = "SELECT *,MAX(date)AS last_date 
				FROM user_info LEFT JOIN schedule_info 
				ON user_info.id=schedule_info.user_id 
				GROUP BY(user_info.id) 
				HAVING (last_date IS NULL OR last_date<\"$today\")
                ORDER BY last_date DESC";
       */
      
      			$sql = "SELECT * 
				FROM user_info
				WHERE user_info.id NOT IN(
					SELECT schedule_info.user_id
    				FROM schedule_info
    				WHERE(schedule_info.date>\"$today\" AND schedule_info.date< \"$next_week\")   
				)
                ORDER BY id";
        
        		$list = Db::query($sql);
        		$this->assign('arealist',$list);
       			$user_type=array("0"=>"普通用户","1"=>"院领导","2"=>"部门领导","3"=>"系领导");
       			$this->assign('user_type',$user_type);
        		return $this->fetch();
    }
}