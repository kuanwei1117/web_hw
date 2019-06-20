<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/13
 * Time: 19:38
 */

namespace app\common\model;

use think\Db;
use think\Model;

class Common extends Model
{

    public function get_menu_id($module,$controller,$action){
        $menu_id = Db::table('menu')->where('module',$module)
            ->where('controller',$controller)
            //->where('action',$action)
            ->field('menu_id')
            ->find();
        return $menu_id['menu_id'];
    }
    public function get_menu_info(){
        $menu_info = Db::table('menu')->where('is_delete',0)
            ->order('number')
            ->select();
        return $menu_info;
    }
}