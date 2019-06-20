<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:40
 */

namespace app\adminmanage\controller;
use app\common\controller\Common;
use app\adminmanage\model\ManageInfo as ManageInfoModel;
use think\Config;
use \think\Request;
use app\adminmanage\controller\Auth;
use think\Db;

class Adminbasic extends Common
{
    // admins list
  public function index(){
    // get admin group
    $auth = new Auth();
    
    // get all admin
    // show 10 admins per page
    //$admin_list = ManageInfoModel::paginate(100);
    $resp['current_status'] = -1;
    // $admin_list = ManageInfoModel::order('is_delete ace, username, id')->paginate(1000);
    $admin_list = Db::name('manage_info')->alias('a') ->join('manage_auth_group_access b','a.id = b.uid')->order('is_delete ace, group_id ace, username, id')->select();
    //dump($data); die;

    //dump($admin_list);die;
    //$admin_list = db('manage_info')->order('is_delete desc, id') -> select();
    if(input('?get.status')){
      $status = Request::instance()->param('status');
      //dump($status); die;
      if ((int)$status >= 0){
        // $admin_list = ManageInfoModel::order('is_delete ace, username, id')->where('is_delete',$status) -> paginate(1000);
        $admin_list = Db::name('manage_info')->alias('a') ->join('manage_auth_group_access b','a.id = b.uid')->where('is_delete',$status)->order('is_delete ace, group_id ace, username, id')->select();
        //$admin_list = db('manage_info') -> where('is_delete',$status) ->order('is_delete desc, id') -> select();
        $resp['current_status'] = (int)$status;
      }
    }
    
    $index = 0;
    for ($x = 0; $x < count($admin_list); $x++) {
      $_group_title = $auth -> getGroups($admin_list[$x]['id']);
      if ($_group_title){
        $group_title = $_group_title[0]['title'];
        $admin_list[$x]['group_title'] = $group_title;
        $admin_list[$x]['group_id'] = $_group_title[0]['group_id'];
        $admin_list[$x]['index'] = $x + 1;
      }
      else{
        $admin_list[$x]['group_title'] = '未设置组权限';
        $admin_list[$x]['group_id'] = 9999;
        $admin_list[$x]['index'] = $x + 1;
      }
      
    } 
    // dump($admin_list); die;
    
    //dump($admin_list['data']); die;
    // foreach ($admin_list as $k => $v){
    //   //var_dump($v['id']);
    //   $_group_title = $auth -> getGroups($v['id']);
    //   if ($_group_title){
    //     $group_title = $_group_title[0]['title'];
    //     $v['group_title'] = $group_title;
    //     $v['group_id'] = $_group_title[0]['group_id'];
 
    //   }else{
    //     $v['group_title'] = '未设置组权限';
    //     $v['group_id'] = 9999;
    //   }
    // }
    // $admin_list = $admin_list->toarray()['data'];
    
    

    // usort($admin_list, function ($item1, $item2) {
    //   if ($item1['group_id'] == $item2['group_id']) return 0;
    //   return $item1['group_id'] > $item2['group_id'] ? -1 : 1;
    // });

    // usort($admin_list, function ($item1, $item2) {
    //   if ($item1['is_delete'] == $item2['is_delete']) return 0;
    //   return $item1['is_delete'] > $item2['is_delete'] ? -1 : 1;
    // });
    

    

    $resp['admin_list'] = $admin_list;
    $resp['status_list'] = Config::get('STATUS');
    $this -> assign('resp', $resp);
    $this -> assign('admin_id', ADMIN_ID);
    return $this -> fetch('index');
  }
  // add an admin member
  public function add(){
    // date_default_timezone_set("Asia/Shanghai");
    // dump(date_default_timezone_get()); die;
    // check method is Post
    if (request() -> isPost()){
      $data = [
        'username' => input('admin_name'),
        'telephone' => input('admin_phone'),
        'password' => input("admin_password"),
        'is_delete' => 0,
        'update_time' => date('Y-m-d H:i:s')
      ];

      if (trim($data['username']) !== $data['username']){
        return $this->error('管理员名称前后不得包含空格');
      }

      $validate = \think\Loader::validate('ManageInfo');
      if(!$validate -> scene('add') -> check($data)){
        $this -> error($validate -> getError());
        die;
      }else{
        // 加密管理员帐号
        $data['password'] = md5($data['password']);
      }

      if(db('manage_info') -> insert($data)){
        //insert into group access
        $current_user = db('manage_info') -> where('username',input('admin_name')) -> find();
        $add_grp_acc = db('manage_auth_group_access') -> insert(['uid' => $current_user['id'], 'group_id' => input('group_id')]);
        if($add_grp_acc){
          return $this->success('添加管理员成功', 'index');
        }else{
          return $this->error('添加管理员失败');
        }
      }else{
        return $this->error('添加管理员失败');
      }




      return;

    }
    $auth_group_list = db('manage_auth_group') -> where('status', 1) -> select();
    $this -> assign('auth_group_list', $auth_group_list);
    return $this -> fetch('add');
  }

  // edit admin info
  public function edit(){
    $auth = new Auth();
    if (request() -> isPost()){
      $id = input('id');
      $admin = db('manage_info') -> find($id);
      $username = input('admin_name');
      $group_id = input('group_id');
      if (!$group_id){
        $_group_title = $auth -> getGroups($id);
        if ($_group_title){
          $group_id = $_group_title[0]['group_id'];
        }else{
          $this -> error("OOPS! 发生错误");
        }
      }
      if (!$username){
        $username = $admin['username'];
      }

      $data = [
        'id' => input('id'),
        'username' => $username,
        'update_time' => date('Y-m-d H:i:s')
      ];
      if(input('admin_password')){
          $data['password'] = md5(input('admin_password'));
      }else{
          $data['password'] = $admin['password'];
      }

      if(input('admin_phone')){
          $data['telephone'] = input('admin_phone');
      }else{
          $data['telephone'] = $admin['telephone'];
      }

      $validate = \think\Loader::validate('ManageInfo');
        if (!$validate -> scene('edit') -> check($data)){
          $this -> error($validate -> getError()); die;
      }

      $save = db('manage_info') -> update($data);
        if($save !== false){
          //insert into group access
          //dump(['uid' => input('id'), 'group_id' => input('group_id')]); die;
          $add_grp_acc = db('manage_auth_group_access') -> where(array('uid' => input('id'))) -> update(['group_id' => $group_id]);
          if($add_grp_acc !== false){
            return $this->success('编辑管理员成功', 'index');
          }else{
            return $this->error('编辑管理员失败');
          }
          $this->success('修改成功', 'index');
        }else{
          $this->error('修改失败');
        }

      return;
    }

    $id = input('id');
    $admin_id = ADMIN_ID;

    // 检测是否为超级管理员
    $_group_title = $auth -> getGroups($admin_id);
    if (!$_group_title){
      $this -> error("OOPS! 发生错误");
    }else if($_group_title[0]['group_id'] !== 1 && $id != $admin_id){
      $this -> error("您没有权限");
    }

    $disable = true;
    if ($_group_title[0]['group_id'] == 1){
      $disable = false;
    }

    $admin = db('manage_info') -> find($id);
    $this -> assign('admin', $admin);
    $auth_group_list = db('manage_auth_group') -> where('status', 1) -> select();
    $this -> assign('auth_group_list', $auth_group_list);
    //query group access
    $auth_grp_access = db('manage_auth_group_access') -> where(array('uid' => $id)) -> find();
    $this -> assign('group_id', $auth_grp_access['group_id']);
    $this -> assign('disable', $disable);
    return $this -> fetch('edit');
  }

  // delete admin
  public function del(){
    $id = input('id');
    $admin = db('manage_info') -> find($id);
    $auth = new Auth();
    $admin_groupId = $auth -> getGroups($id);
    // dump($admin_groupId); die;
    // if (!array_key_exists(0, $admin_groupId)){
    //   dump("NULL"); die;
    // } else {
    //   dump($admin_groupId[0]['group_id']); die;
    // }

    if (array_key_exists(0, $admin_groupId)){
      if ($admin_groupId[0]['group_id'] == 1){
        // dump($admin_groupId[0]['group_id']); die;
        $this -> error('禁止删除超级管理员', 'index');
      }
    }

    if($admin['is_delete'] == 0){
      // 更新数据表中的数据
      if (db('manage_info')->where('id',$id)->update(['is_delete' => 1, 'delete_time' => date('Y-m-d H:i:s')])){
        $this -> success('删除成功', 'index');
      }else{
        $this -> error('删除失败', 'index');
      }

    }else{
      $this -> error('删除失败', 'index');
    }
  }

  public function recover(){
    $id = input('id');
    $admin = db('manage_info') -> find($id);
    
    if($admin['is_delete'] == 1){
      // 更新数据表中的数据
      if (db('manage_info')->where('id',$id)->update(['is_delete' => 0])){
        $this -> success('恢复成功', 'index');
      }else{
        $this -> error('恢复失败', 'index');
      }

    }else{
      $this -> error('恢复失败', 'index');
    }
  }
}