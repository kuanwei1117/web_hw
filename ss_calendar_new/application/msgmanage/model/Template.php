<?php
/**
 * Created by 佟起.
 * User: 
 * Date: 2019/4/18
 */

namespace app\msgmanage\model;
use think\Model;
use think\Db;
use app\logmanage\model\Log as LogModel;

class Template extends Model
{
    //绑定表名
    /* protected $table = 'message_template';
    protected $pk = 'id'; */

    /*story:查询消息模板
    负责人：吴珏
    */
    //根据模板名称获取模板
    public function getItemByTitle($tit){
        $titleTemp = Db::name('message_template')
            ->where('title','like','%'.$tit.'%')
            ->where('is_delete',0)
            ->select();
        return $titleTemp;
    }

    //根据模板内容获取模板
    public function getItemByContent($content){
        $contentTemp = Db::name('message_template')
            ->where('content','like','%'.$content.'%')
            ->where('is_delete',0)
            ->select();
        return $contentTemp;
    }
    //获取所有模板
    public function getAllTemplates(){
        $allItems = Db::name('message_template')
            ->where('is_delete',0)
            ->select();
        return $allItems;
    }
    //获取所有删除模板
    public function getAllTemplatesDelete(){
        $allItems = Db::name('message_template')
            ->where('is_delete',1)
            ->select();
        return $allItems;
    }
    //根据模板名称或内容获取模板
    public function getAllItems($tit){
        $allItems = Db::name('message_template')
            ->where('content|title','like','%'.$tit.'%')
            ->where('is_delete',0)
            ->select();
        return $allItems;
    }
    //根据模板名称获取模板
    public function getItemByTitleDelete($tit){
        $titleTemp = Db::name('message_template')
            ->where('title','like','%'.$tit.'%')
            ->where('is_delete',1)
            ->select();
        return $titleTemp;
    }
    //根据模板内容获取模板
    public function getItemByContentDelete($content){
        $contentTemp = Db::name('message_template')
            ->where('content','like','%'.$content.'%')
            ->where('is_delete',1)
            ->select();
        return $contentTemp;
    }
    //获取所有模板
    public function getAllItemsDelete($tit){
        $allItems = Db::name('message_template')
            ->where('content|title','like','%'.$tit.'%')
            ->where('is_delete',1)
            ->select();
        return $allItems;
    }
    //恢复模板
    public function renewTemplate($id){
        $res = Db::name("message_template")->where('id',$id)->update(['is_delete'=>0,'update_time'=> date('Y-m-d H:i:s',time())]);
        return $res;
    }

    /*story:增加消息模板
    负责人：佟起
    */
    //根据模板名称获取模板，非模糊查询
    public function strictGetItemByTitle($tit){
        $Temp = Db::name('message_template')
            ->where('title',$tit)
            ->where('is_delete',0)
            ->select();
        return $Temp;
    }

    public function strictGetItemByContent($cont){
        $Temp = Db::name('message_template')
            ->where('content',$cont)
            ->where('is_delete',0)
            ->select();
        return $Temp;
    }


    public function insertTemplate($tit, $cont){
        
        $uid = ADMIN_ID; // 获取当前管理员ID
        //插入数据
        $data = ['title' => $tit, 'content'=> $cont, 'creat_id'=> $uid, 'is_delete' => 0,'update_time'=> date('Y-m-d H:i:s',time())];
        $res = Db::name('message_template')->insertGetId($data);

        $model = new LogModel();
        $type = 2;
        $is_manage = 1; // 管理员填1,非管理员填0
        $table = 'message_template';
        $field = [$res]; // 增加的主键列表，不是学号
        $isRcd = 0;

        //记录日志
        if($res){
            $isRcd = $model->recordLogApi ($uid, $type, $is_manage, $table, $field); //需要判断调用是否成功
        }
        
        return $isRcd;
    }

    /**
     * Created by 张骁雄.
     * User:
     * Date: 2019/4/21
     */
    //删除模板
    public function clearTemplate($id){
        $res = Db::name("message_template")->where('id',$id)->update(['is_delete'=>1,'delete_time'=> date('Y-m-d H:i:s',time()),'update_time'=> date('Y-m-d H:i:s',time())]);
        return $res;
    }
    //更新模板
    public function updateTemplate($id,$des,$content){
        $res = Db::name("message_template")->where('id',$id)->update(['title'=>$des,'content'=>$content,'update_time'=> date('Y-m-d H:i:s',time()),'delete_time'=> date('Y-m-d H:i:s',time())]);
        return $res;
     }
      
    /*
    *story:根据消息模板向用户发送提醒消息（刘玄）
    细分story：发送消息提醒
    *负责人：刘玄
    */
    function remind($user_id)
    {
        $data = ['is_remind' => 1,'update_time'=> date('Y-m-d H:i:s',time()),'remind_time'=> date('Y-m-d H:i:s',time())];
        $res = Db::name('message_template')
            ->where('id',$user_id)
            ->update($data);
        return $res;
    }
        /*
    *story:根据消息模板向用户发送提醒消息（刘玄）
    细分story：发送消息提醒
    *负责人：刘玄
    */
    function message($user_id)
    {
        $res = Db::name('message_template')
            ->where('id',$user_id)->field("title,content")
            ->select();
        return $res;
    }
    /*
    *story:根据消息模板向用户发送提醒消息（刘玄）
    细分story：取消发送消息提醒
    *负责人：刘玄
    */

    function cancelremind($user_id)
    {
         $data = ['is_remind' => 0,'update_time'=> date('Y-m-d H:i:s',time()),'cancelremind_time'=> date('Y-m-d H:i:s',time())];
         $res = Db::name('message_template')
            ->where('id',$user_id)
            ->update($data);
         return $res;
    }
    /*
    *story:根据消息模板向用户发送提醒消息（刘玄）
    细分story：向客户端发送消息内容
    *负责人：刘玄
    */
  public  function remindToApp()
    {
        
        $data = Db::name('message_template')
            ->where('is_delete',0) ->where('is_remind',1)
            ->select();
        return $data;
    }

}