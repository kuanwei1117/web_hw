<?php
/**
 * Created by PhpStorm.
 * User: 李梦好
 * Date: 2019/4/9
 * Time: 19:55
 */

namespace app\usermanage\model;

use think\Model;
use think\Db;
use app\logmanage\model\Log as LogModel;

class Department extends Model
{
    //绑定表名
    protected $table = 'user_depart';
    protected $pk = 'id';
    protected $name = 'name';

    /*
     *story:恢复删除的部门
     *负责人：李梦好
     */
    public function recover($id)
    {
        $department = Department::get($id);
        //$department = Department::where("id",$id)->find();
        $myname = $department->getAttr("name");
        $dep = Department::all(['name'=>$myname]);
        if(count($dep)>1){
            return ['status' => -1, 'message' => '该部门已存在'];
        }
        //更新该记录的is_delete字段
        $department->data(['is_delete' => 0]);
        $department->save();//保存，也就是把更新提交到数据库表*/
        return ['status' => 1, 'message' => 'success'];
    }

    /*
     *story:删除部门
     *负责人：张欣童
     */
    public function setDelete($id)
    {
        $department = Department::get($id);
        //更新该记录的is_delete字段
        $department->is_delete = '1';
        $department->save();//保存，也就是把更新提交到数据库表*/
    }

    /*
     *story:
     *负责人：
     */
    public function getAllDepartments()
    {
        $departs = Db::query('select id, name, is_delete from user_depart order by is_delete,name');
        return $departs;
    }

    /*
       *story:添加部门
       *负责人：陆韵
       */
    public function addDepartment($name)
    {
        // 接收用户的数据,部门描述
		#protected $_validate = array(
        #array('name','require','部门名称不能为空'，0,'regex',3),
        #);
        if (Department::get(['name' => $name])) {
            //如果在表中查询到该用户名
            $status = 0;
            $message = '部门已存在,请重新输入';
            return ['status' => $status, 'message' => $message];
        }
		if(empty($name)){
          //如果输入的部门名称为空值
          $message = '部门名称不能为空';
    	return ['message' => $message];
		}
      if(strlen($name) > 30){
      //如果部门名称大于25个字符
        $message = '部门名称太长';
        return ['message' => $message];
      }
      if(!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u',$name)){
      #如果输入的部门包含标点
         $message = '部门名称中不能包含标点符号';
    	return ['message' => $message];
      }
        $user = model('Department');
        // 模型对象赋值
        $user->data([
            'name' => $name,
            'is_delete' => 0
        ]);
        $user->save();
        $status = 1;
        $message = '添加成功';
      	return ['status' => $status, 'message' => $message];
        
    }
 

    /*
   *story:修改部门名称
   *负责人：张艺璇
   */
    public function change($id, $myname)
      
    {
      //判断部门名是否含有标点和空格
      if(!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u',$myname)){
         $message = '部门名称中不能包含标点符号';
    	return -3;
      }
        $department = Department::get($id);//可以通过此种方式根据别的字段获取记录
      //判断用户名是否重复
        $pre = Department::where('name', $myname)->find();
        if (empty($pre)==false){
            return -1;
        }
        if(strlen($myname) > 30){
      //如果部门名称大于25个字符
        $message = '部门名称太长';
        return -2;
      }
        $department->save([
            'name' => $myname
        ], ['id' => $id]);
        return 1;
    }

}