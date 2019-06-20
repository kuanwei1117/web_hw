<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:42
 */

namespace app\adminmanage\controller;

use app\adminmanage\model\ManageAuthRule as ManageAuthRuleModel;


use app\adminmanage\model\ManageAuthRule;
use app\common\controller\Common;

class Power extends Common
{
    public function index()
    {
        $auth_rule = new ManageAuthRuleModel();
        if (request()->isPost()) {
            $sorts = input('post.');

            foreach ($sorts as $k => $v) {
                $auth_rule->update(['id' => $k, 'sort' => $v]);
            }
            $this->success('更新排序成功', 'index');
            return;
        }
        $auth_rule_list = $auth_rule->authRuleTree();
        $index = 0;
        foreach ($auth_rule_list as $k => $v){
            $v['index'] = $index+1;
            $index = $index + 1;
        }
        $this->assign('auth_rule_list', $auth_rule_list);
        return $this->fetch('index');
    }


    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');

            // 验证「控/方」「权限名」是否重复
            $validate = \think\Loader::validate('ManageAuthRule');
            if (!$validate->check($data)) {
                $this->error($validate->getError(), null, null, 1);
                die;
            }

            $pre_level = db('manage_auth_rule')->where('id', $data['pid'])->field('level')->find();
            if ($pre_level) {
                $data['level'] = $pre_level['level'] + 1;
            }

            $add = db('manage_auth_rule')->insert($data);
            if ($add) {
                $this->success("添加权限成功", 'index');
            } else {
                $this->error("添加权限失败");
            }

            return;
        }
        $auth_rule = new ManageAuthRuleModel();
        $auth_rule_list = $auth_rule->authRuleTree();
        $this->assign('auth_rule_list', $auth_rule_list);
        return $this->fetch('add');
    }

    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            // 验证「控/方」「权限名」是否重复
            $validate = \think\Loader::validate('ManageAuthRule');
            if (!$validate->check($data)) {
                $this->error($validate->getError(), null, null, 1);
                die;
            }
            $pre_level = db('manage_auth_rule')->where('id', $data['pid'])->field('level')->find();
            if ($pre_level) {
                $data['level'] = $pre_level['level'] + 1;
            } else {
                $data['level'] = 0;
            }
            $save = db('manage_auth_rule')->update($data);
            if ($save !== false) {
                $this->success('修改权限成功', 'index');
            } else {
                $this->error('修改权限失败');
            }
            return;
        }

        $auth_rule = new ManageAuthRuleModel();
        $auth_rule_list = $auth_rule->authRuleTree();
        $auth_rule = $auth_rule->find(input('id'));
        $this->assign(
            array(
                'auth_rule_list' => $auth_rule_list,
                'auth_rule' => $auth_rule
            )
        );
        return $this->fetch('edit');
    }


    public function del()
    {
        $auth_rule = new ManageAuthRuleModel();
        $authRuleIds = $auth_rule->getchilrenid(input('id'));
        $authRuleIds[] = input('id');
        $del_count = 0;
        foreach ($authRuleIds as $authRuleId) {
            $del = db('manage_auth_rule')->where('id', $authRuleId)->update(['status' => 0]);
            $del_count = $del_count + $del;
        }

        if ($del_count === count($authRuleIds)) {
            $this->success('删除权限成功');
        } elseif ($del_count !== 0) {
            $this->error('权限尚未全部删除成功');
        } elseif ($del_count == 0) {
            $this->error('删除权限失败');
        }
    }

}