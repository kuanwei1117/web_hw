<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:56
 */

namespace app\login\controller;

use app\logmanage\model\Log as LogModel;
use think\Controller;
use think\Db;
use app\login\model\Mobile as Suibian;

//http://localhost/ss_calendar_new/public/index.php/login/phonelogin/index
class Phonelogin extends Controller
{
    /**
     * 王婉蓉
     * 功能：刷新页面，更新登录按钮状态，判断验证码跳转到新的页面
     * @return view
     */
    public function index()
    {
        //return $this -> success('登入成功123', 'index/index/index');
        //$this -> success('登入成功', 'querystatistics/query/index', null, 1);
        if (request()->isAjax()) {
            $tel = trim(input('tel'));
            $code = trim(input('code'));

            $admin = Db::table('manage_info')->where('telephone', $tel)->find();

            //验证码输入正确
            if ( !empty($tel) && !empty($code) && $tel == cookie('tel') && $code == cookie('Code')){
                // 寫入日誌
                $model = new LogModel();
                $type = 1;
                $is_manage = 1;
                $res = $model->recordLogApi($admin['id'], $type, $is_manage);
                if ($res){
                    $msg=['status'=>0,'msg'=>'登陆成功'];
                    return json($msg);
                }
            }else{
                $msg=['status'=>1,'msg'=>'登陆失败'];
                return json($msg);
            }
       }
        return $this->fetch();
    }
    /**
     * 王婉蓉
     * 功能：判断是否为管理员并发送短信
     */
    //注意：目前测试次数用完了所以会发送失败= =
    public function sendCode(){
        //dump("sendCOde");
        //dump('sendCone');
        if (request()->isAjax()){
            /* 前端的电话号码 */
            $tel=trim(input('phoneNum'));

            /* 判断是否为管理员号码 */
            $def = new Suibian();
            $is_manager = $def -> hasMobile($tel);

            /* 是管理员则发送短信 */
            if($is_manager){
//                $msg=['status'=>0,'msg'=>'是管理员'];
//                return json($msg);
                $code = mt_rand(10000,99999);
                $res = $this->aip($tel,$code);
                if($res == 0){
                    cookie('tel',$tel,60);
                    cookie('Code',$code,60);
                    $msg=['status'=>0,'msg'=>'短信发送成功'];
                    return json($msg);
                }else{
                    cookie('tel', null);
                    cookie('Code', null);
                    $msg=['status'=>$res,'msg'=>'短信发送失败'];
                    return json($msg);
                }
            }else{
                //echo '不是管理员';
                $msg=['status'=>1,'msg'=>'该号码不正确'];
                return json($msg);
            }
        }
    }

    /**
     * 王婉蓉
     * 功能：请求第三方 API （短信宝）发送短信
     * @return  第三方平台返回的结果
     */
    public function aip($tel,$code,$time = 1){

        $smsapi = "http://www.smsbao.com/"; //短信网关
        $user = "annora"; //短信平台帐号
        $pass = md5("370683"); //短信平台密码
        $content="【北大软微】您的验证码为{$code}，请尽快输入！";//要发送的短信内容
        $phone = $tel;
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result =file_get_contents($sendurl) ;
        return $result;
    }
}