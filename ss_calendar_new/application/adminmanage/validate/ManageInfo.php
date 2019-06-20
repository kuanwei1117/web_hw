<?php
namespace app\adminmanage\validate;
use think\Validate;
class ManageInfo extends Validate
{

    protected $regex = [ 'zip' => '/^1[3|4|5|8][0-9]{9}$/'];
    protected $rule = [
      'username' => 'require|max:25|unique:manage_info',
      'telephone' => 'require|regex:1[3-9]{1}[0-9]{9}',
      'password' => ['regex' => '/(?!^\d+$)(?!^[A-Za-z]+$)(?!^[^A-Za-z0-9]+$)^\S{8,30}$/']
      //'password' => 'require|min:8'
    ];
    

    protected $message = [
      'username.require' => '必须输入管理员名称',
      'username.max' => '管理员名称不得超过25个字符',
      'password.require' => '必须输入管理员密码',
      'telephone' => '请输入正确的手机号格式',
      'password.regex' => '密码格式错误'
    ];



    protected $scene = [
      'add' => ['username', 'password', 'telephone'],
      'edit' => ['username' => 'require', 'telephone' => 'require|regex:1[3-9]{1}[0-9]{9}']
    ];
}
