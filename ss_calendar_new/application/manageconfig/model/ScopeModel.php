<?php

namespace app\manageconfig\model;

define ('day_to_second',86400);

use think\Model;
use think\Db;
use app\logmanage\model\Log as LogModel;

class ScopeModel extends Model
{   
    public static $scope_table = 'global_config';
    public static $uid = ADMIN_ID;
    public static $is_manager = 1;
    //初始化日程范围配置
    public function initScope()
    {
        # code...
        $model = new LogModel();
        $type = 2;
        
        $default = ['config_item' => 'scope', 'parameter' => 2592000];
        $exi = DB::table('global_config')->where('config_item', 'scope')->count();
        if ($exi > 0) {
            $init_success = 1;
        } else {
            $init_success = DB::table('global_config')->insert($default);
            $id = DB::table('global_config')->getLastInsID();
            $field = [$id];
            $model->recordLogApi(SELF::$uid,$type,SELF::$is_manager,SELF::$scope_table,$field);
         }

        return $init_success; //成功返回1
    }
    //读取日程范围配置
    public function getScope()
    {
        # code...
        $scope = DB::table('global_config')->where('config_item', 'scope')->find();
        return $scope;
    }
    //修改日程范围配置
    public function editScope($parameter)
    {
        # code...
        $model = new LogModel();
        $type = 3;
        $id = DB::table('global_config')->getLastInsID();
        $before_update =  DB::table('global_config')->where('config_item', 'scope')->find();
        $before_update_para_day = $before_update['parameter'] / day_to_second;
        $edit_success = DB::table('global_config')->where('config_item', 'scope')->update(['parameter' => $parameter]);
        $after_update_para_day = $parameter / day_to_second;
        $field = [
            $id=>[$before_update_para_day,$after_update_para_day]
        ];
        $model->recordLogApi(SELF::$uid,$type,SELF::$is_manager,SELF::$scope_table,$field);
        return $edit_success; //成功返回影响的条数（设定应该成功为1）
    }
}
