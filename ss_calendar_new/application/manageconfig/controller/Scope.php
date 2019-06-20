<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:26
 */

namespace app\manageconfig\controller;

use think\Model;
use app\common\controller\Common;
use think\Db;
use think\Request;
use app\manageconfig\model\ScopeModel;

class Scope extends Common
{
    public function index()
    {
        $scope_model = new ScopeModel();
        $scope_model->initScope();

        $last_scope = $scope_model->getScope();
        $this->assign('last_scope', $last_scope['parameter'] / 86400);
        return $this->fetch();
    }
    public function scopeModify()
    {
        // 获取post数据        
        $scope = Request::instance()->param('scope');
        $scope_model = new ScopeModel();

        $data = array();
        $data['Scope'] = $scope * day_to_second; //转化为以秒为单位

        $success_edit = $scope_model->editScope($data['Scope']);
        if($success_edit == 1)
        return 0;
        else
        return 1;
    }
}
