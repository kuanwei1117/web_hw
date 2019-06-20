<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:50
 */

namespace app\msgmanage\controller;


use app\common\controller\Common;

class Msgmodel extends Common
{
     /*
    *story:根据消息模板向用户发送提醒消息（刘玄）
    细分story：向客户端发送消息内容
    *负责人：刘玄
    */
    // const API_KEY = 'F8D23F9B6A4AA3F2';
    // const API_SECRET = '8307ED503A6D58E4733D01FC459E340B';
    const APP_KEY = 'F8D23F9B6A4AA3F2';
    const SCHOOL_CODE = '1016145360';
    const APP_SECRET = '8307ED503A6D58E4733D01FC459E340B';
    public function getInfo() {
        // $media_id = 'gh_594c04b29acc';
        //$open_url = 'http://weixiao.qq.com/common/get_media_info';
        $user_id=113;

        $tmp = model('Template');
        $res=$tmp->message($user_id);
        // $testtitle=$res[0]['title'];
        // $tescon=$res[0]['content'];
        $open_url='https://uni.weixiao.qq.com/open/notice/send';
       
        $title=$res[0]['title'];
        $content=$res[0]['content'];
        $sender="教务处";
        $cards=array("1801210380");
        $digest="xx";
        $t=json_encode($cards);
        $customs=array("You have a notice to check","https:\/\/weixiao.qq.com");
        $h=json_encode($customs);
        $param_array = array(
            'school_code' => self::SCHOOL_CODE,
            'cards' =>  "$t",
            'title' => "$title",
            'content' => "$content",
            'sender' => "$sender",
            'app_key' => self::APP_KEY,
            'timestamp' => time(),
            'nonce' => $this->genNonceStr(),
            
        );
 
        $param_array['signature'] = $this->calSign($param_array);
        $reponse = $this->post($open_url, $param_array);
        echo  json_encode($param_array,JSON_UNESCAPED_UNICODE) ; 
        echo   $reponse ; 
        print_r($res)    ; 
        // print_r($testtitle)    ; 
        // print_r($tescon)    ; 
    }
 
    /**
     * 生成32位随机字符串
     * @return string
     */
    public function genNonceStr() {
        return strtoupper(md5(time() . mt_rand(0, 10000) . substr('abcdefg', mt_rand(0, 7))));
    }
 
    /**
     * curl post 请求
     * @param string $url
     * @param string $json_data json字符串
     * @return json
     */
    public function post($url, $json_data, $https = true) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
           curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
 
    /**
     * 计算签名
     * @param array $param_array
     * @return string
     */
    public function calSign($param_array) {
        $names = array_keys($param_array);
        sort($names, SORT_STRING);
 
        $item_array = array();
        foreach ($names as $name) {
            $item_array[] = "{$name}={$param_array[$name]}";
        }
 
        $str = implode('&', $item_array) . '&key=' . self::APP_SECRET;
        return strtoupper(md5($str));
    }
    /*
    *story:查询消息模板
    *负责人：吴珏
    */
    public function index(){
        $model = model('Template');
        $search = "";
        $status = -2;
        $range = -2;
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
        }
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
        }
        if (isset($_GET['range'])) {
            $range = $_GET['range'];
        }
        if($status==-2 && $range == -2){
            $templateItems = $model->getAllTemplates();
            $this->assign('templateItems',$templateItems);
            return $this->fetch();
        }
        else{
            if($status==1 && $search==""){
                $templateItems = $model->getAllTemplates();
            }
            else if($status==2 && $search==""){
                $templateItems = $model->getAllTemplatesDelete();
            }
            else if($status==1 && $range==1){
                $templateItems = $model->getItemByTitle($search);
            }
            else if($status==1 && $range==2){
                $templateItems = $model->getItemByContent($search);
            }
            else if($status==2 && $range==1){
                $templateItems = $model->getItemByTitleDelete($search);
            }
            else if($status==2 && $range==2){
                $templateItems = $model->getItemByContentDelete($search);
            }
            else if($status==1 && $range==0){
                $templateItems = $model->getAllItems($search);
            }
            else if($status==2 && $range==0){
                $templateItems = $model->getAllItemsDelete($search);
            }
            if ($templateItems == null) {
                $this->error("搜索项不存在，请重新尝试");
            }
            else{
                $this->assign('templateItems',$templateItems);
                return $this->fetch();
            }
        }
    }

    public function loadTemplate()
    {
        $template = model('Template');
        $templates = $template->getAllTemplates();
        return $templates;
    }
    
    public function searchTemplate($search,$status,$range)
    {
        $model = model('Template');
        if($status==-1){
            $this->error("请选择查询状态");
        }
        else if($range==-1){
            $this->error("请选择查询范围");
        }
        else{
            if($status==2 && $search==""){
                $isHasTitle = $model->getAllTemplates();
            }
            else if($status==1 && $search==""){
                $isHasTitle = $model->getAllTemplatesDelete();
            }
            else if($status==1 && $range==1){
                $isHasTitle = $model->getItemByTitleDelete($search);
            }
            else if($status==1 && $range==2){
                $isHasTitle = $model->getItemByContentDelete($search);
            }
            else if($status==2 && $range==1){
                $isHasTitle = $model->getItemByTitle($search);
            }
            else if($status==2 && $range==2){
                $isHasTitle = $model->getItemByContent($search);
            }
            else if($status==1 && $range==0){
                $isHasTitle = $model->getAllItems($search);
            }
            else if($status==2 && $range==0){
                $isHasTitle = $model->getAllItemsDelete($search);
            }
            if ($isHasTitle == null) {
                $this->error("搜索项不存在，请重新尝试");
            }
            else{
                $this->assign('templateItems',$isHasTitle);
                return $this->fetch();
            }
        }
    }
    public function enableTemplate(){
        $id = $_POST['id'];
        $tit = $_POST['tit'];
        $model = model('Template');
        $isHasSame = $model->strictGetItemByTitle($tit);
        if($isHasSame==null){
            $res = $model->renewTemplate($id);
            if($res == 1)
                $this->success("恢复成功");
            else
                $this->success("恢复失败");
        }
        else{
            $this->success("已存在相同标题，恢复失败");
        }
        
    }
    /*
     *story:添加消息模板
     *负责人：佟起
     */
    public function  addTemplate()
    {
        $tit = $_POST['tit'];
        $con = $_POST['con'];
        $regTit = '/^[\x{4e00}-\x{9fa5}a-z][\x{4e00}-\x{9fa5}a-z\d\s]{0,29}[\x{4e00}-\x{9fa5}a-z\d]$/u'; 
        $regCon = '/[\x{4e00}-\x{9fa5}A-Za-z]/u';
        if(preg_match($regTit,$tit) && strlen($tit)<=140 && preg_match($regCon,$con)){  //验证标题格式 
            $model = model('Template');
            $isHasSame = $model->strictGetItemByTitle($tit);
            $isHasSameContent = $model->strictGetItemByContent($con);
            //$isHasSame = null;
            if ($isHasSame == null && $isHasSameContent == null) {
                $res = $model->insertTemplate($tit, $con);
                if($res){
                    $this->success("新增成功");
                }
                else{
                    $this->error("添加失败，请重新尝试");
                }
            }
            else{
                if($isHasSame){
                    $this->error("模板标题已存在");
                }
                if($isHasSameContent){
                    $this->error("模板内容已存在");
                }
            }
        }
        else{
            $this->error("添加失败，请重新尝试"); 
        }
    }
    /*
     *story:删除消息模板
     *负责人：张骁雄
     */
    public function  deleteTemplate(){
        $id = $_POST['id'];
        $model = model('Template');
        $res = $model->clearTemplate($id);
        if($res == 1)
            $this->success("删除成功");
        else
            $this->success("删除失败");
    }
    /*
    *story:修改消息模板
    *负责人：张骁雄
    */
    public function modifyTemplate(){
        $id = $_POST['id'];
        $des = $_POST['des'];
        $content = $_POST['content'];
        $model = model('Template');
        $res = $model->updateTemplate($id,$des,$content);
        if($res==1)
            $this->success("修改成功");
        else
            $this->success("修改失败");
    }

    /*
    *story:根据消息模板向用户发送提醒消息（刘玄）
    细分story：发送消息提醒
    *负责人：刘玄
    */
    public function remind()
    {
        $user_id = $_POST['user_id'];
       // $work_id = $_POST['work_id'];
        $open_url='https://uni.weixiao.qq.com/open/notice/send';
        $tmp = model('Template');
        $res=$tmp->message($user_id);
       
        $title=$res[0]['title'];
        $content=$res[0]['content'];
        $sender="教务处";
        $cards=array("1801210380");
        $digest="xx";
        $t=json_encode($cards);
        $customs=array("You have a notice to check","https:\/\/weixiao.qq.com");
        $h=json_encode($customs);
        $param_array = array(
            'school_code' => self::SCHOOL_CODE,
            'cards' =>  "$t",
            'title' => "$title",
            'content' => "$content",
            'sender' => "$sender",
            'app_key' => self::APP_KEY,
            'timestamp' => time(),
            'nonce' => $this->genNonceStr(),
            
        );
 
        $param_array['signature'] = $this->calSign($param_array);
        $reponse = $this->post($open_url, $param_array);
        // echo  json_encode($param_array,JSON_UNESCAPED_UNICODE) ; 
        // echo   $reponse ; 
        //echo   $work_id ;
      
      
       
     
        $position = model('Template');
        $resm=$position->remind($user_id);
        if($resm == 1)
            $this->success("发送消息提醒成功");
        else
            $this->success("发送消息提醒失败");
    }
     /*
    *story:根据消息模板向用户发送提醒消息（刘玄）
    细分story：取消发送消息提醒
    *负责人：刘玄
    */
    public function cancelremind()
    {
        $user_id = $_POST['user_id'];
        $position = model('Template');
        $res=$position->cancelremind($user_id);
        if($res == 1)
            $this->success("取消消息提醒成功");
        else
            $this->success("取消消息提醒成功");
    }
    /*
    *story:根据消息模板向用户发送提醒消息（刘玄）
    细分story：向客户端发送消息内容
    *负责人：刘玄
    */

    public function remindToApp()
    {
     

        $res= model('Template');
        $dateres = $res->remindToApp();
        $res_success = json_encode($dateres);
    
        header('Content-Type:application/json');//这个类型声明非常关键
        return $res_success;
    

    }

}
