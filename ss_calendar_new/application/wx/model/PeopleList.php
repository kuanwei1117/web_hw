<?php
namespace app\wx\model;
use think\Model;
use think\Db;


class PeopleList extends Model
{
    /**
     * @purpose：
     *  与user_info数据库进行交互
     * @Author 第09组 沈安强
     * @Date 2019-4-25
     *
     */
	protected $table = 'user_info';

	// 获取领导列表
	public function peopleList(){
	    $items = new PeopleList();
		$res = $items->where('is_delete = 0 AND type_id != 0')->select();
		return $res;
	}

}