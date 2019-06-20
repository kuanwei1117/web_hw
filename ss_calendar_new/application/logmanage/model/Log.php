<?php
/**
 * Created by 03_group.
 */
namespace app\logmanage\model;

use think\Model;
use think\Db;

use think\Request;

class ClientInfo extends Model{
    public function getLang() {
        $Lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
        //使用substr()截取字符串，从 0 位开始，截取4个字符
        if (preg_match('/zh-c/i',$Lang)) {
            //preg_match()正则表达式匹配函数
            $Lang = '简体中文';
        }
        elseif (preg_match('/zh/i',$Lang)) {
            $Lang = '繁體中文';
        }
        else {
            $Lang = 'English';
        }
        return $Lang;
    }

    public function getBrowser() {
        $user_OSagent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_OSagent, "Maxthon") && strpos($user_OSagent, "MSIE")) {
            $visitor_browser = "Maxthon(Microsoft IE)";
        } elseif (strpos($user_OSagent, "Maxthon 2.0")) {
            $visitor_browser = "Maxthon 2.0";
        } elseif (strpos($user_OSagent, "Maxthon")) {
            $visitor_browser = "Maxthon";
        } elseif (strpos($user_OSagent, "Edge")) {
            $visitor_browser = "Edge";
        } elseif (strpos($user_OSagent, "Trident")) {
            $visitor_browser = "IE";
        } elseif (strpos($user_OSagent, "MSIE")) {
            $visitor_browser = "IE";
        } elseif (strpos($user_OSagent, "MSIE")) {
            $visitor_browser = "MSIE";
        } elseif (strpos($user_OSagent, "NetCaptor")) {
            $visitor_browser = "NetCaptor";
        } elseif (strpos($user_OSagent, "Netscape")) {
            $visitor_browser = "Netscape";
        } elseif (strpos($user_OSagent, "Chrome")) {
            $visitor_browser = "Chrome";
        } elseif (strpos($user_OSagent, "Lynx")) {
            $visitor_browser = "Lynx";
        } elseif (strpos($user_OSagent, "Opera")) {
            $visitor_browser = "Opera";
        } elseif (strpos($user_OSagent, "MicroMessenger")) {
            $visitor_browser = "WeiXinBrowser";
        } elseif (strpos($user_OSagent, "Konqueror")) {
            $visitor_browser = "Konqueror";
        } elseif (strpos($user_OSagent, "Mozilla/5.0")) {
            $visitor_browser = "Mozilla";
        } elseif (strpos($user_OSagent, "Firefox")) {
            $visitor_browser = "Firefox";
        } elseif (strpos($user_OSagent, "U")) {
            $visitor_browser = "Firefox";
        } elseif (strpos($user_OSagent, "Safari/")) {
            $visitor_browser = "Safari";
        } else {
            $visitor_browser = "Other Browser";
        }
        return $visitor_browser;
    }

    public function getOS() {
        $OS = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/win/i',$OS)) {
            $OS = 'Windows';
        }
        elseif (preg_match('/mac/i',$OS)) {
            $OS = 'MAC';
        }
        elseif (preg_match('/linux/i',$OS)) {
            $OS = 'Linux';
        }
        elseif (preg_match('/unix/i',$OS)) {
            $OS = 'Unix';
        }
        elseif (preg_match('/bsd/i',$OS)) {
            $OS = 'BSD';
        }
        else {
            $OS = 'Other';
        }
        return $OS;
    }
  
  public function getIP()
    {
        static $realip = NULL;

        if ($realip !== NULL)
        {
            return $realip;
        }

        if (isset($_SERVER))
        {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip)
                {
                    $ip = trim($ip);

                    if ($ip != 'unknown')
                    {
                        $realip = $ip;

                        break;
                    }
                }
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP']))
            {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }
            else
            {
                if (isset($_SERVER['REMOTE_ADDR']))
                {
                    $realip = $_SERVER['REMOTE_ADDR'];
                }
                else
                {
                    $realip = '0.0.0.0';
                }
            }
        }
        else
        {
            if (getenv('HTTP_X_FORWARDED_FOR'))
            {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            }
            elseif (getenv('HTTP_CLIENT_IP'))
            {
                $realip = getenv('HTTP_CLIENT_IP');
            }
            else
            {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

        return $realip;
    }
  
  
  
    public function getIP1() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //如果变量是非空或非零的值，则 empty()返回 FALSE。
            $IP = explode(',',$_SERVER['HTTP_CLIENT_IP']);
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $IP = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
        }
        elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $IP = explode(',',$_SERVER['REMOTE_ADDR']);
        }
        else {
            $IP[0] = 'None';
        }
        return $IP[0];
    }

    private function getAddIsp() {
        $IP = $this->getIP();
        $AddIsp = mb_convert_encoding(file_get_contents('http://open.baidu.com/ipsearch/s?tn=ipjson&wd='.$IP),'UTF-8','GBK');
        //mb_convert_encoding() 转换字符编码。
        if (preg_match('/noresult/i',$AddIsp)) {
            $AddIsp = 'None';
        }
        else {
            $Sta = stripos($AddIsp,$IP) + strlen($IP) + strlen('来自');
            $Len = stripos($AddIsp,'"}')-$Sta;
            $AddIsp = substr($AddIsp,$Sta,$Len);
        }
        $AddIsp = explode(' ',$AddIsp);
        return $AddIsp;
    }

    public function findCityByIp($ip){
        $data = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip);
        return json_decode($data,$assoc=true);
    }

    public function getAdd() {
        $Add = $this->getAddIsp();
        return $Add[0];
    }

    public function getIsp() {
        $Isp = $this->getAddIsp();
        if ($Isp[0] != 'None' && isset($Isp[1])) {
            $Isp = $Isp[1];
        }
        else {
            $Isp = 'None';
        }
        return $Isp;
    }
}

class Log extends Model
{
    /**
     * 杨宇 董亚聪 苏恒杰
     * 功能：记录web端和企业微信端的登录/增加/修改/删除日志
     * @param
     * @uid：操作人的主键id，非学号
     * @type: 1登陆 2增加 3修改 4删除
     * @table ：操作的数据表名，如操作的数据表为'user_info'，则 $table = 'user_info'
     * @field ：操作的数据表的内容数组
     * $is_manage: 1表示管理员，从web过来的；0用户，从企业微信端过来的。
     * @return int
     * 1登陆: 只需要传入$uid, $type
     * 2增加：需要传入$uid, $type, $table, $field(该字段传入你增加的所有数据的主键，如 $field = ['11'，'12'])
     * 3修改：假如同时操作了数据表中主键为22和23的两条数据的field1和field2字段, 则 $field = ['22'=>['field1'=> ['before value', 'after value'], 'field2'=> ['before value', 'after value']],'23'=>['field1'=> ['before value', 'after value'], 'field2'=> ['before value', 'after value']]]
     * 4删除：需要传入$uid, $type, $table, $field(该字段传入你删除的所有数据的主键，如 $field = ['11'，'12'])
     */
    public function recordLogApi($uid, $type, $is_manage = 1, $table = '', $field = ''){
        if(!is_numeric($uid)) {
            echo "recordLogApi fail, invalid uid!";
            return 0;
        }elseif(!is_numeric($type) || ($type != 1 && $type != 2 && $type != 3 && $type != 4)) {
            echo "recordLogApi fail, invalid type!";
            return 0;
        }elseif(!is_numeric($is_manage) || ($is_manage != 0 && $is_manage != 1)) {
            echo "recordLogApi fail, invalid is_manage!";
            return 0;
        }elseif(!empty($table) && !is_string($table)) {
            echo "recordLogApi fail, invalid table, table must be string!";
            return 0;
        }elseif($field && !is_array($field)) {
            echo "recordLogApi fail, invalid field, field must be array!";
            return 0;
        }

        $client = new ClientInfo();
        $ip = $client->getIP();
        $agent = [
            'os' => $client->getOS(),
            'brower' => $client->getBrowser(),
        ];

        $action = [
            'table' => $table,
            'id_list' => $field,
        ];

        if($type == 1) {
            $data = ['is_manage' => $is_manage,'user_id' => $uid, 'operate_type' => $type, 'operate_time' => date('Y-m-d H:i:s', time()), 'user_agent' => json_encode($agent), 'ip' => $ip];
        }else{
            $data = ['is_manage' => $is_manage,'user_id' => $uid, 'operate_type' => $type, 'operate_time' => date('Y-m-d H:i:s', time()), 'operate_action' => json_encode($action), 'user_agent' => json_encode($agent), 'ip' => $ip];
        }
        $res = Db::name('log_user')->insert($data);
        return $res;
    }

    /**
     * 贺文鑫
     * 功能：根据本人的工号或学号或管理员id号来查询日志
     * @param $uid
     * @return list
     */
    public function getLogByUid($uid){
        $nameItem = Db::name('log_user')
            ->where('user_id',$uid)
            ->where('is_manage',1)
            ->order("log_user.operate_time desc")
            ->select();
        return $nameItem;
    }

    /**
     * 杨宇
     * 功能：查询所有非管理员日志
     * @return array
     */
    public function getAllUserLog(){
        $list = Db::query('SELECT user_info.id,log_user.user_id,log_user.operate_time,log_user.operate_type,log_user.operate_action,log_user.user_agent,log_user.ip FROM log_user,user_info WHERE log_user.user_id = user_info.id AND log_user.is_manage = 0 order by log_user.operate_time desc ');
        /*$list = Db::table('log_user')
            ->alias('l')
            ->join('user_info u', 'l.user_id = u.id')
            ->where("u.is_delete=0")
            ->order("l.operate_time desc")
            ->select();*/
        return $list;
    }

    /**
     * 徐辉
     * 功能：查询所有管理员日志
     * @return array
     */
    public function getAllManagerLog(){
        $list = Db::query('SELECT manage_info.id,log_user.user_id,log_user.operate_time,log_user.operate_type,log_user.operate_action,log_user.user_agent,log_user.ip FROM log_user,manage_info WHERE log_user.user_id = manage_info.id AND log_user.is_manage = 1 order by log_user.operate_time desc ');
        /*$list = Db::table('log_user')
            ->alias('l')
            ->join('manage_info m', 'l.user_id = m.id')
            ->where("m.is_delete=0")
            ->order("l.operate_time desc")
            ->select();*/
        return $list;
    }
}
