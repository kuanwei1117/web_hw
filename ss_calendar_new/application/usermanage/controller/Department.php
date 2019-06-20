<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:36
 */

namespace app\usermanage\controller;
use app\common\controller\Common;

class Department extends Common
{
    public function index(){
      	$department = model('Department');
        $departments = $department->getAllDepartments();
        $this->assign('departlist',$departments);
        return $this->fetch();
    }
    /*
     *story:恢复删除的部门
     *负责人：李梦好
     */
    public function recover($id)
    {
        //调用model中的recover方法，保证MVC分离
        $department = model('Department');
        $departments = $department->getAllDepartments();
        $this->assign('departlist',$departments);
        return $department->recover($id);
    }
    /*
     *story:删除部门
     *负责人：张欣童
     */
    public function delete($id)
    {
        $department = model('Department');
        $department->setDelete($id);
        dump($department);die;
        $departments = $department->getAllDepartments();
        $this->assign('departlist',$departments);

    }

    /*
     *story:
     *负责人：
     */
    public function loadDepartment()
    {
        $department = model('Department');
        $departments = $department->getAllDepartments();
        return $departments;
    }

    /*
     *story:添加部门
     *负责人：陆韵
     */
    public function add($name)
    {
        #$name = D('name')
        $department = model('Department');
        $departments = $department->getAllDepartments();
        $this->assign('departlist',$departments);
        return $department->addDepartment($name);
    }
    /*
     *story:修改部门名称
     *负责人：张艺璇
     */
    //public function change($id,$name)
  //  {
   //     $department = model('Department');
  //      $department->change($id,$name);
   //     error_log(print_r($id));
   //     error_log(print_r($name));
//
   // }
  	public function change($id,$name)
    {
        $department = model('Department');
        $departments = $department->getAllDepartments();
        $this->assign('departlist',$departments);
        return $department->change($id,$name);
    }
}