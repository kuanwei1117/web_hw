<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/17
 * Time: 14:31
 */

namespace app\msgmanage\controller;


use app\common\controller\Common;

/**
* @ Purpose:
* 查看并修改用户隐私条款和使用条款
* @Author: Qu Qian
* @Date:2019/4/4
* @Time: 20:27
*/
class Policy extends Common
{
    /**
    * @Purpose:
    * 获取查看并修改用户隐私条款和使用条款的页面
    *
    * @Method Name: index()
    *
    * @Author: Qu Qian
    *
    * @Return: html页面
    */
    public function index()
    {   
        $last = "";
        if (file_exists("provisions.txt")) {
            $provisionFile = fopen("provisions.txt", "r") or die("Unable to open file!");
            if ($provisionFile) {
                $size = filesize("provisions.txt");
                // 防止最近一次写入失败时 $size 为 0，fread 报错
                if ($size) {
                    $last = fread($provisionFile, $size);
                }
                fclose($provisionFile);
            }
        }
        $this->assign('last', $last);
        return $this->fetch();
    }

    /**
    * @Purpose:
    * 修改用户隐私条款和使用条款
    *
    * @Method Name:  postProvisionDetail()
    *
    * @Param: text $textarea-input 用户隐私条款和使用条款内容
    *
    * @Author: Qu Qian
    *
    * @Return: json 修改结果信息
    */
    public function postProvisionDetail()
    {
        $data = input('textarea-input');
        if(!$data){
            return json(['msg' => '输入内容不能为空', 'code' => -1]);            
        } 
        $provisionFile = fopen("provisions.txt", "w") or die("Unable to open file!");
        $tag = fwrite($provisionFile, $data);
        fclose($provisionFile);
        if ($tag) {
            return json(['msg' => '成功！', 'code' => 1]);
        } else {
            return json(['msg' => '失败！', 'code' => 0]);
        }
    }
}