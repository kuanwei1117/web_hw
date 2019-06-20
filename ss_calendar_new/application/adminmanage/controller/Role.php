<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:41
 */

namespace app\adminmanage\controller;

use think\Config;
use app\adminmanage\model\ManageAuthGroup as ManageAuthGroupModel;
use think\Request;

use app\common\controller\Common;

class Role extends Common
{
    public function index()
    {
        $auth_group_list =  ManageAuthGroupModel::paginate(10);
        $index = 0;
        foreach ($auth_group_list as $k => $v){
            $v['index'] = $index+1;
            $index = $index + 1;
        }
        $resp['auth_group_list'] = $auth_group_list;
        $this->assign('resp', $resp);
        return $this->fetch('index');
    }

    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            //dump(array_key_exists("rules", $data)); die;
            //验证没有选择rule
            if (!array_key_exists("rules", $data)) {
                $this->error("权限不得为空", null, null, 1);
            }

            if (!empty($data['rules'])) {
                $data['rules'] = implode(',', $data['rules']);
            } else {
                $data['rules'] = '';
            }
            // check status switch
            $status = isset($_POST['status']);
            if ($status == true) {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }

            $save = db('manage_auth_group')->update($data);
            if ($save !== false) {
                $this->success('修改用户组成功', 'index');
            } else {
                $this->error('修改用户组失败');
            }
        }
        //query the group needed edit
        $auth_rule = new \app\adminmanage\model\ManageAuthRule();
        $auth_rule_list = $auth_rule->authRuleTree();
        $this->assign('auth_rule_list', $auth_rule_list);
        $auth_group = db('manage_auth_group')->find(input('id'));
        $this->assign('auth_group', $auth_group);

        return $this->fetch('edit');
    }

    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');

            if (trim($data['title']) == "") {
                $this->error("用户组名称不得為空", null, null, 1);
            }

            //验证没有选择rule
            if (!array_key_exists("rules", $data)) {
                $this->error("权限不得为空", null, null, 1);
            }

            if ($data['rules']) {
                $data['rules'] = implode(',', $data['rules']);
            }
            // check status switch
            $status = isset($_POST['status']);
            if ($status == true) {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }


            $add = db('manage_auth_group')->insert($data);
            if ($add) {
                $this->success('添加用户组成功', 'index');
            } else {
                $this->error('添加用户组失败');
            }
            return;
        }


        //get methods
        $auth_rule = new \app\adminmanage\model\ManageAuthRule();
        $auth_rule_list = $auth_rule->authRuleTree();
        $this->assign('auth_rule_list', $auth_rule_list);
        return $this->fetch('add');
    }


    //del group
    public function del()
    {
        $del = db('manage_auth_group')->where('id', input('id'))->update(['status' => 0]);

        if ($del) {
            $this->success('删除用户组成功', 'index');
        } else {
            $this->error('删除用户组失败');
        }
    }

    //recover deleted group
    public function recover()
    {
        $del = db('manage_auth_group')->where('id', input('id'))->update(['status' => 1]);

        if ($del) {
            $this->success('恢復用户组成功', 'index');
        } else {
            $this->error('恢復用户组失败');
        }
    }
}