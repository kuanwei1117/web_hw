<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:21
 */

namespace app\wx\controller;
use app\common\controller\Common;
use app\wx\model\UserFollow;
use think\Db;
use think\Request;

class Follow extends Common
{
    /**
     * @purpose：
     *  用户添加关注人
     * @Author 第09组 沈安强
     * @Date 2019-4-25
     *
     */
    //获取关注的领导人的日程
    public function index(){
        $list = Db::table('user_follow')
            ->alias(['user_follow' => 'a', 'user_info' => 'b', 'user_position' => 'c'])
            ->where('a.is_delete',0)
            ->join('user_info','a.follow_id = b.id')
            ->join('user_position','b.position_id = c.id')
            ->field('a.id as id, a.follow_id as userid, b.name as name, c.name as position')
            ->select();
        $this->assign('list_time_table',$list);
        return $this->fetch();
    }


    //返回未关注的领导人员的列表，可供选择关注人
    public function peopleList()
    {
        $result = true;
        if($result){
            $this->success('成功！','follow/leaderList',true,1);
        } else {
            return  $this->error('失败');
        }
    }

    public function leaderList(){
        $UserFollow = new UserFollow();
        $condition = $UserFollow->where('is_delete = 0 AND user_id = 1')->column('follow_id');
        $list = Db::table('user_info')
            ->alias(['user_info' => 'a', 'user_position' => 'b'])
            ->where("a.id","not in",$condition)
            ->join('user_position','a.position_id = b.id')
            ->field('a.id as id, a.name as name, b.name as position')
            ->select();
        $this->assign('list_time_table',$list);
        return $this->fetch('leaderList');
    }

    public function search()
    {
        $id = Request::instance()->param('id');
        $result = true;
        if($result){
            //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
            $this->success('成功！','Follow/search',$id,1);
        } else {
            //错误页面的默认跳转页面是返回前一页，通常不需要设置
            return  $this->error('失败');
        }
    }

    //查询某个关注领导的日程
    public function searchDate()
    {
        $id = Request::instance()->param('id');
        $info = Db::table('schedule_info')
            ->alias(['schedule_info' => 'a', 'user_info' => 'b', 'user_position' => 'c', 'schedule_time' => 'd', 'schedule_place' => 'e', 'schedule_item' => 'f'])
            ->where('a.is_delete',0)
            ->where("user_id",$id)
            ->join('user_info','a.user_id = b.id')
            ->join('user_position','b.position_id = c.id')
            ->join('schedule_time','a.time_id = d.id')
            ->join('schedule_place','a.place_id = e.id')
            ->join('schedule_item','a.item_id = f.id')
            ->field('d.name as time, e.name as place, f.name as item, b.id as userid')
            ->select();

        $name = Db::table('user_info')
            ->where('id',$id)
            ->column('name');

        $this->assign('who',$name[0]);
        $this->assign('info',$info);

        return $this->fetch('search');
    }


    //不再关注
    public function noFollow()
    {
        $id = Request::instance()->param('id');
        $UserFollow = new UserFollow();
        $ans = $UserFollow->get($id);
        $ans->is_delete = 1;
        $res = $ans->validate(true)->save();
        if($res)
        {
            return "更新成功";
        }
        return "更新失败";
    }
	//再关注
    public function againFollow()
    {
        $id = Request::instance()->param('id');
        $UserFollow = new UserFollow();
        $ans = $UserFollow->get($id);
        $ans->is_delete = 0;
        $res = $ans->validate(true)->save();
        if($res)
        {
            return "更新成功";
        }
        return "更新失败";
    }


    //增加关注人
    public function addFollow()
    {
        $followid = Request::instance()->param('followid');
        $UserFollow = new UserFollow();
        // 查询符合条件的时段描述
        $add = [];
        $add['user_id'] = 1;
        $add['follow_id'] = $followid;
        $add['is_delete'] = 0;
        $res = $UserFollow->data($add)->save();
        if($res)
        {
            return "添加成功";
        }
            return "添加失败";
    }
  
  //查看历史关注人
 	public function wx_history(){
        $list = Db::table('user_follow')
                    ->alias(['user_follow' => 'a', 'user_info' => 'b', 'user_position' => 'c'])
                    ->where('a.is_delete',1)
                    ->join('user_info','a.follow_id = b.id')
                    ->join('user_position','b.position_id = c.id')
                    ->field('a.id as id, a.follow_id as userid, b.name as name, c.name as position')
                    ->select();
        $this->assign('list_time_table',$list);
        return this->fetch();
    }


}