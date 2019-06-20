<?php


namespace app\logmanage\controller;

use app\common\controller\Common;
use app\logmanage\model\Log as LogModel;

class Selflog extends Common
{
    public function index(){

        $model = new LogModel();
        $uid = ADMIN_ID;  // app/common/controlier，管理员id：`ADMIN_ID`，管理員username：`ADMIN_NAME`
        $info = $model->getLogByUid($uid);

        foreach($info as $key=>$value){
            if ($info[$key]['operate_type'] == 1){
                $info[$key]['type'] = '登录';
                $info[$key]['id_list'] = '';
                $info[$key]['table'] = '';
            }elseif ($info[$key]['operate_type'] == 2){
                $info[$key]['type'] = '添加';
            }elseif ($info[$key]['operate_type'] == 3){
                $info[$key]['type'] = '修改';
            }elseif ($info[$key]['operate_type'] == 4){
                $info[$key]['type'] = '删除';
            }

            $info[$key]['os'] = json_decode($info[$key]['user_agent'],true)['os'];
            $info[$key]['brower'] = json_decode($info[$key]['user_agent'],true)['brower'];

            if($info[$key]['operate_type'] != 1) {
                $info[$key]['id_list'] = json_encode(json_decode($info[$key]['operate_action'], true)['id_list']);
                $info[$key]['table'] = json_decode($info[$key]['operate_action'], true)['table'];
            }
        }

        $this->assign('info',$info);
        return $this->fetch();
    }
}