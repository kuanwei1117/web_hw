<?php

namespace app\usermanage\model;

use think\Model;
use think\Db;

class Position extends Model
{
    //绑定表名
    protected $table = 'user_position';
    protected $pk = 'id';
    protected $name = 'name';

    /**
     * 第05组 高裕欣
     * 功能：显示列表
     */
    public function getUserPositionList()
    {
        $list = Db::table('user_position')
            ->select();
        return $list;
    }

    /**
     *第05组 张君兰
     * 功能：修改职位
     */
  public function change($id, $name)
    {
        if (Position::get(['name' => $name])) {
            //如果在表中查询到该用户名
            $status = 0;
            $message = '职位已存在,请重新输入';
            return ['status' => $status, 'message' => $message];
        }
        if(empty($name)){
            //如果输入的职位名称为空
            $message = '职位名称不能为空';
            return ['message' => $message];
        }
        if(strlen($name) > 30){
            //如果职位名称大于30个字符
            $message = '职位名称不能大于30个字符';
            return ['message' => $message];
        }
        if(!preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_（）()]+$/u',$name)){
            #如果输入包含标点符号字符
            $message = '职位名称中不能包含标点符号';
            return ['message' => $message];
        }
        $position = Position::get($id);
        $position->save(
            ['name' => $name],
            ['id' => $id]
        );
        $status = 1;
        $message = "编辑成功";
        return['status' => $status, 'message' => $message];
    }
   /* public function change($id, $name)
    {
        $data = array();
        $data['is_delete'] = 0;
        $data['delete_time'] = Db::raw('now()');
        Db::table('user_position')
            ->where('id', $id)
            ->update(['name' => $name]);

        return $name;
    }*/

    /**
     * 第05组 高裕欣
     * 功能：作废职位
     */
    function invalid($user_id)
    {
        $data = array();
        $data['is_delete'] = 1;
        $data['delete_time'] = Db::raw('now()');
        Db::table('user_position')->where('id', $user_id)->update($data);
    }

    function restore($user_id)
    {
        $data = array();
        $data['is_delete'] = 0;
        $data['delete_time'] = Db::raw('now()');
        Db::table('user_position')->where('id', $user_id)->update($data);
    }

    /**
     * 第05组 张楚悦
     * 功能：添加职位
     */
    public function insertPosition($name)
    {
        $data = ['name' => $name];//, 'is_delete' => 0
        $result = Db::name('user_position')->insert($data);
        return $result;
    }
    public function getPosition($name)
    {
        $positionName = Db::name('user_position')
            ->where('name', $name)
            //->where('is_delete', 0)
            ->find();
        return $positionName;
    }
}




