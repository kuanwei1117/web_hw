<?php
namespace app\wx\model;
use think\Model;
use think\Db;


class UserFollow extends Model
{
    /**
     * @purpose：
     *  与user_follow数据库进行交互
     * @Author 第09组 沈安强
     * @Date 2019-4-25
     *
     */
	protected $table = 'user_follow';

	public function getMyFollow(){
	    $items = new UserFollow();
		$res = $items->where('is_delete = 0 AND user_id = 1')->select();
		return $res;
	}

}