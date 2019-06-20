<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 9:42
 */

namespace app\wx\common;


use think\Controller;
use think\Db;

class Common extends Controller
{
    //检测用户信息，是否登录

    //权限管理

    //access token初始化

    /**
    *检查是否有查看日程的权限
     * @return boolean 有权限的话返回true，没有则false
     */
    public static function checkViewScheduleAuthority($user_id){
        $res=Db::table("user_info")->where('id',$user_id)->
        where('is_delete',0)->find();
        return !empty($res);
    }
    /**
     *检查是否有修改日程的权限
     * @return boolean 有权限的话返回true，没有则false
     */
    public static function checkManageScheduleAuthority($user_id){
        $res=Db::table("white_list")->where('user_id',$user_id)->
        where('is_delete',0)->find();
        return !empty($res);
    }
}