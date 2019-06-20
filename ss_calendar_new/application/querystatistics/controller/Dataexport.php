<?php

namespace app\querystatistics\controller;
use app\common\controller\Common;
use think\Controller;
use think\Request;
use think\Db;


class Dataexport extends Common
{
    public function index(){
        return $this->fetch('index');
    }
    public function export(){
//1.从数据库中取出数据
        $start = request()->post("start");
        $end = request()->post("end");
        $list = Db::name('schedule_info')->where('date',"<",$end)->where('date',">", $start )-> where('is_delete',"=", 0)->select();
        //2.加载PHPExcle类库
        vendor('PHPExcel.PHPExcel');
        //3.实例化PHPExcel类
        $objPHPExcel = new \PHPExcel();
        //4.激活当前的sheet表
        $objPHPExcel->setActiveSheetIndex(0);
        //5.设置表格头（即excel表格的第一行）

        //设置F列水平居中
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('A')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('B')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('C')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('D')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('E')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('F')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('G')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('H')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('I')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('J')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('K')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('L')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle('M')->getAlignment()
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', '用户姓名')
            ->setCellValue('C1', '用户学号')
            ->setCellValue('D1', '用户部门')
            ->setCellValue('E1', '用户职位')
            ->setCellValue('F1', '日期')
            ->setCellValue('G1', '时间')
            ->setCellValue('H1', '日程地点')
            ->setCellValue('I1', '日程事项')
            ->setCellValue('J1', '事项描述')
            ->setCellValue('K1', '创建时间')
            ->setCellValue('L1', '更新时间')
            ->setCellValue('M1', '删除时间');

        //6.循环刚取出来的数组，将数据逐一添加到excel表格。
        for($i=0;$i<count($list);$i++){
            $user_info = Db::name("user_info")->where(["id" => $list[$i]['user_id']])->find();
            $user_name =$user_info['name'];
            $user_workid =$user_info['work_id'];
            $user_departid =$user_info['depart_id'];
            $user_positionid =$user_info['position_id'];
            $user_date = $list[$i]['date'];
            $user_depart = Db::name("user_depart")->where(["id" => $user_departid])->find()['name'];
            $user_position = Db::name("user_position")->where(["id" => $user_positionid])->find()['name'];

            $user_time = Db::name("schedule_time")->where(["id" => $list[$i]['time_id']])->find()['name'];
            $user_place = Db::name("schedule_place")->where(["id" => $list[$i]['place_id']])->find()['name'];
            $user_item = Db::name("schedule_item")->where(["id" => $list[$i]['item_id']])->find()['name'];
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2),$list[$i]['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2),$user_name);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2),$user_workid);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+2),$user_depart);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),$user_position);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+2),$user_date);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+2),$user_time);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.($i+2),$user_place);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.($i+2),$user_item);
            $objPHPExcel->getActiveSheet()->setCellValue('J'.($i+2),$list[$i]['note']);
            $objPHPExcel->getActiveSheet()->setCellValue('K'.($i+2),$list[$i]['create_time']);
            $objPHPExcel->getActiveSheet()->setCellValue('L'.($i+2),$list[$i]['update_time']);
            $objPHPExcel->getActiveSheet()->setCellValue('M'.($i+2),$list[$i]['delete_time']);
        }
        //7.设置保存的Excel表格名称
        $filename = '日程信息'.date('ymdhis',time()).'.xls';
        //8.设置当前激活的sheet表格名称；
        $objPHPExcel->getActiveSheet()->setTitle('日程信息');
        //9.设置浏览器窗口下载表格
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$filename.'"');
        ob_end_clean();
        ob_start();
        //生成excel文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //下载文件在浏览器窗口
        $objWriter->save('php://output');
        exit;
    }

    public function exportuser()
    {
        //1.从数据库中取出数据
        header("Content-type:text/html;charset=utf-8");
        $input = request()->post("username");
        $start = request()->post("start");
        $end = request()->post("end");
        if ($input == '') {
            $this->error("请输入用户姓名或工号");
        } else {
            if (is_numeric($input)) {
                $user_id = Db::name('user_info')->where(["work_id" => intval($input)])->find()['id'];

            } else {
                $user_id = Db::name('user_info')->where(["name" => $input])->find()['id'];
            }
            if ($user_id == '') {
                $this->error("该用户不存在或输入有误");}
            else {
                $list = Db::name('schedule_info')->where(["user_id" => $user_id])->where('date',"<",$end)->where('date',">", $start )-> where('is_delete',"=", 0)->select();
                echo('ssss');
                //2.加载PHPExcle类库
                vendor('PHPExcel.PHPExcel');
                //3.实例化PHPExcel类
                $objPHPExcel = new \PHPExcel();
                //4.激活当前的sheet表
                $objPHPExcel->setActiveSheetIndex(0);
                //5.设置表格头（即excel表格的第一行）

                //设置F列水平居中
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('A')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('B')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('C')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('D')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('E')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('F')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('G')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('H')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('I')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('J')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('K')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('L')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle('M')->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', '用户姓名')
                    ->setCellValue('C1', '用户学号')
                    ->setCellValue('D1', '用户部门')
                    ->setCellValue('E1', '用户职位')
                    ->setCellValue('F1', '日期')
                    ->setCellValue('G1', '时间')
                    ->setCellValue('H1', '日程地点')
                    ->setCellValue('I1', '日程事项')
                    ->setCellValue('J1', '事项描述')
                    ->setCellValue('K1', '创建时间')
                    ->setCellValue('L1', '更新时间')
                    ->setCellValue('M1', '删除时间');

                //6.循环刚取出来的数组，将数据逐一添加到excel表格。
                for ($i = 0; $i < count($list); $i++) {
                    $user_info = Db::name("user_info")->where(["id" => $list[$i]['user_id']])->find();
                    $user_name = $user_info['name'];
                    $user_workid = $user_info['work_id'];
                    $user_departid = $user_info['depart_id'];
                    $user_positionid = $user_info['position_id'];
                    $user_date = $list[$i]['date'];
                    $user_depart = Db::name("user_depart")->where(["id" => $user_departid])->find()['name'];
                    $user_position = Db::name("user_position")->where(["id" => $user_positionid])->find()['name'];
                    $user_time = Db::name("schedule_time")->where(["id" => $list[$i]['time_id']])->find()['name'];
                    $user_place = Db::name("schedule_place")->where(["id" => $list[$i]['place_id']])->find()['name'];
                    $user_item = Db::name("schedule_item")->where(["id" => $list[$i]['item_id']])->find()['name'];
                    $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2),$list[$i]['id']);
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2),$user_name);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2),$user_workid);
                    $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+2),$user_depart);
                    $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2),$user_position);
                    $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+2),$user_date);
                    $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+2),$user_time);
                    $objPHPExcel->getActiveSheet()->setCellValue('H'.($i+2),$user_place);
                    $objPHPExcel->getActiveSheet()->setCellValue('I'.($i+2),$user_item);
                    $objPHPExcel->getActiveSheet()->setCellValue('J'.($i+2),$list[$i]['note']);
                    $objPHPExcel->getActiveSheet()->setCellValue('K'.($i+2),$list[$i]['create_time']);
                    $objPHPExcel->getActiveSheet()->setCellValue('L'.($i+2),$list[$i]['update_time']);
                    $objPHPExcel->getActiveSheet()->setCellValue('M'.($i+2),$list[$i]['delete_time']);
                }
                //7.设置保存的Excel表格名称
                $filename = '用户历史日程' . date('ymdhis', time()) . '.xls';
                //8.设置当前激活的sheet表格名称；
                $objPHPExcel->getActiveSheet()->setTitle('日程信息');
                //9.设置浏览器窗口下载表格
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");
                header('Content-Disposition:inline;filename="' . $filename . '"');
                ob_end_clean();
                ob_start();
                //生成excel文件
                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                //下载文件在浏览器窗口
                $objWriter->save('php://output');
                exit;
            }
        }
    }
}
