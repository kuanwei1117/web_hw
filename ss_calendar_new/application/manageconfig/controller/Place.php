<?php

namespace app\manageconfig\controller;


use app\common\controller\Common;
use app\manageconfig\model\LocationManage as LocationModel;
use think\Request;

class Place extends Common
{
    public function index(){
    	$location_model = new LocationModel();
    	$list = $location_model->getAllLocationInfo();
    	$this->assign('arealist',$list);
        return $this->fetch();
    }

    //添加新地点
    public function addNewArea(){
        $location_model = new LocationModel();
    	$param = Request::instance()->param();
    	$area = trim($param['areaName']);

    	$info = $location_model->getLocationInfoByName($area);
    	//求数据条目数
    	$temp = count($info);
    	if($temp != 0)
    		return 3;
    	$info2 = $location_model->insertLocationInfo($area);
    	if($info2)
    		return 0;
    	else
    		return 1;
    }

    //删除地点信息
    public function deleteArea(){
    	$param = Request::instance()->param();
        $location_model = new LocationModel();
    	$id = trim($param['id']);
    	$info = $location_model->deleteLocationInfoByID($id);
    	//$info = DB::table('schedule_place')->where('id',$id)->delete();
    	if($info)
    		return 0;
    	else
    		return 1;
    }

    //修改地点信息
    public function changeArea(){
    	$param = Request::instance()->param();
        $location_model = new LocationModel();
    	$id = trim($param['id']);
    	$area = trim($param['area']);
    	//var_dump($area);die();
    	$info = $location_model->getLocationInfoByName($area);
    	//求数据条目数
    	$temp = count($info);
    	if($temp != 0)
    		return 3;
    	$info = $location_model->changeLocationNameByID($id,$area);
    	if($info)
    		return 0;
    	else
    		return 1;
    }
    
    //恢复地点
    public function recoverArea(){
    	$param = Request::instance()->param();
    	$location_model = new LocationModel();
    	$id = trim($param['id']);
    	$info = $location_model->recoverAreaByID($id);
    	if($info)
    		return 0;
    	else
    		return 1;
    }
    
}