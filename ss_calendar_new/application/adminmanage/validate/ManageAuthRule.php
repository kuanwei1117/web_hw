<?php
namespace app\adminmanage\validate;
use think\Validate;
class ManageAuthRule extends Validate
{

    protected $rule = [
      'name' => 'require|unique:manage_auth_rule',
      'title' => 'unique:manage_auth_rule',
    ];
    

    protected $message = [
      'name.unique' => '权限 [控制器/方法] 不得重复',
      'title.unique' => '权限名称不得重复',
      'name.require' => '请输入 [控制器/方法]'
    ];

}
