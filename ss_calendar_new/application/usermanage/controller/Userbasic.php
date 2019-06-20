<?php
/**
 * Created by PhpStorm.
 * User: 84333
 * Date: 2019/4/14
 * Time: 0:34
 */

namespace app\usermanage\controller;


use app\common\controller\Common;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use think\Request;
use app\logmanage\model\Log as LogModel;

//require 'vendor/autoload.php';

class Userbasic extends Common
{
    public function index()
    {
        $userbasic = model('Userbasic');
  
        $info = $userbasic->getinfo();
      
        foreach ($info as $key => $value) {
            if ($info[$key]['type_id'] == 0) {
                $info[$key]['type'] = '普通用户';
            } elseif ($info[$key]['type_id'] == 1) {
                $info[$key]['type'] = '院领导';
            } elseif ($info[$key]['type_id'] == 2) {
                $info[$key]['type'] = '部门领导';
            } elseif ($info[$key]['type_id'] == 3) {
                $info[$key]['type'] = '系领导';
            }
        }
        foreach ($info as $a => $aa) {
            if ($info[$a]['up_isdelete'] == 1) {
                $info[$a]['up_name'] = '空';
            }
        }
        foreach ($info as $b => $bb) {
            if ($info[$b]['ud_isdelete'] == 1) {
                $info[$b]['ud_name'] = '无';
            }
        }
        $this->assign('info', $info);

        //添加人员模态框传值
        $depart = $userbasic->getdepart();
        $this->assign('depart', $depart);
        $position = $userbasic->getposition();
        $this->assign('position', $position);
        return $this->fetch();
    }

    public function edituserinfo()
    {
        $userbasic = model('Userbasic');
        $data = input('post.');
        //dump($data);
        //return $data;
        $isRepeat = $userbasic->validData($data);
        return $isRepeat;
        $is_add = $userbasic->edituserinfo($data);
        if ($is_add) {
            $this->success('修改成功！');
        } else {
            $this->error('修改失败！');
        }
    }

    /**
     * 添加用户
     * 获取到前端传递的用户数据(可缺省为空属性待讨论)
     * 通过学号或工号(work_id)判断待添加用户是否存在
     * 查询到work_id(无论是否被删除)都认为用户存在，提示信息
     * 否则用户不存在，添加用户
     */
    public function addUser()
    {
        $model = new LogModel();
        $userbasic = model("Userbasic");
        $data = input('post.');
        $workId = $data['work_id'];
        $user = $userbasic->findUserByWorkId($workId);
        if ($user != null) {
            $this->error("该用户已被添加(删除)，不可重复添加");
        }
        $addFlag = $userbasic->insertUser($data);
        if ($addFlag) {
            $ret = [];
            $ret[0] = strval($addFlag);
            $logFlag = $model->recordLogApi(ADMIN_ID, 2, 1, 'user_info', $ret);
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * Excel批量添加用户
     * 获取到前端传递的包含用户信息的Excel文件(文件格式将会在前端提示，内部数据合法性暂不处理)
     * 对于每一行获取的用户信息进行用户存在性判断
     * 不存在的用户会被记录，最后批量添加
     */
    public function batchAddByExcel()
    {
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $model = new LogModel();
        try {
            $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die($e->getMessage());
        }

        $sheet = $spreadsheet->getActiveSheet();

        $sqlData = array();

        $ret = array();

        $i = 0;

        $userbasic = model("Userbasic");

        foreach ($sheet->getRowIterator(2) as $row) {
            $tmp = array();
            foreach ($row->getCellIterator() as $cell) {
                $tmp[] = $cell->getFormattedValue();
            }
            // 未被添加的用户信息才会被记录到数组里，最后批量添加
            if ($userbasic->findUserByWorkId($tmp[1]) == null) {
                if ($i == 0) {
                    $first['name'] = $tmp[0];
                    $first['work_id'] = $tmp[1];
                    $first['type_id'] = $tmp[2];
                    $first['depart_id'] = $tmp[3];
                    $first['position_id'] = $tmp[4];
                } else {
                    $tmp = ['name' => $tmp[0],
                        'work_id' => $tmp[1],
                        'type_id' => $tmp[2],
                        'depart_id' => $tmp[3],
                        'position_id' => $tmp[4]];
                    $sqlData[$i - 1] = $tmp;
                }
                $i++;
            }
        }
        $firstFlag = $userbasic->insertUser($first);
        $ret[0] = strval($firstFlag);
        if ($i > 1) {
            $addFlag = $userbasic->insertAllUser($sqlData);
            for ($j = 0; $j < $addFlag; $j++) {
                $ret[$j + 1] = strval($firstFlag + $j + 1);
            }
        }
        if (!empty($ret)) {
            $model->recordLogApi(ADMIN_ID, 2, 1, 'user_info', $ret);
            $this->success('批量添加成功，重复用户信息已自动过滤未添加');
        } else {
            $this->error('添加失败');
        }
    }

    public function exampleExcel() {
        $userbasic = model('Userbasic');
        $depart = $userbasic->getdepart();
        $position = $userbasic->getposition();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', '姓名');
        $sheet->setCellValue('B1', '学工号');
        $sheet->setCellValue('C1', '用户类型[普通用户:0, 院领导:1, 部门领导:2, 系领导:3]');
        $sheet->setCellValue('D1', '所属部门');
        $sheet->setCellValue('E1', '职位');
        $sheet->setCellValue('F1', '右边为映射，不为模板');
        $sheet->setCellValue('G1', '所属部门id(上传)');
        $sheet->setCellValue('H1', '所属部门name');
        $sheet->setCellValue('I1', '职位id(上传)');
        $sheet->setCellValue('J1', '职位name');

        $sheet->setCellValue('A2', '张测试');
        $sheet->setCellValue('B2', '1801210999');
        $sheet->setCellValue('C2', '0');
        $sheet->setCellValue('D2', '1');
        $sheet->setCellValue('E2', '1');
        $i = 2; //从第二行开始
        foreach ($depart as $data) {
            $spreadsheet->getActiveSheet()
                ->setCellValue('G' . $i, $data['id'])
                ->setCellValue('H' . $i, $data['name']);
            $i++;
        }
        $i = 2; //从第二行开始
        foreach ($position as $data) {
            $spreadsheet->getActiveSheet()
                ->setCellValue('I' . $i, $data['id'])
                ->setCellValue('J' . $i, $data['name']);
            $i++;
        }

        $spreadsheet->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(10);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(15);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(15);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('H')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('I')
            ->setWidth(15);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('J')
            ->setWidth(20);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="example.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }



    //删除单个用户
    public function deleteUser() {
        $data = Request::instance()->param('id');
        $userbasic = model("Userbasic");
        $deleteFlag = $userbasic->delwhitelist($data);
        if ($deleteFlag) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function batchDeleteUser() {
        $idArray = $_POST['list'];
        $userbasic = model("Userbasic");
        foreach ($idArray as $item) {
            $userbasic->delwhitelist($item);
        }
        return 'ok';
    }


    //导出指定id的用户信息excel
    public function exportviaid(){
        $idstrings = $_POST['idlist'];
        $idArray = explode(",", $idstrings);
        $info = array();
        foreach ($idArray as $item){
            $tmp_info = model("Userbasic")->getinfoviaid($item);
            array_push($info, $tmp_info);

        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', '用户姓名')
            ->setCellValue('C1', '工号/学号')
            ->setCellValue('D1', '用户类型：')
            ->setCellValue('E1', '所属部门')
            ->setCellValue('F1', '职位');
        $departdict = array(0 => "普通用户", 1 => "院领导", 2 => "部门领导", 3 => "系领导");
        $spreadsheet->getActiveSheet()->setTitle('用户信息');
        //dump($info);
        $i = 2; //从第二行开始
        foreach ($info as $data) {
            $spreadsheet->getActiveSheet()
                ->setCellValue('A' . $i, $data[0]['id'])
                ->setCellValue('B' . $i, $data[0]['ui_name'])
                ->setCellValue('C' . $i, $data[0]['work_id'])
                ->setCellValue('D' . $i, $departdict[$data[0]['type_id']])
                ->setCellValue('E' . $i, $data[0]['ud_name'])
                ->setCellValue('F' . $i, $data[0]['up_name']);

            $i++;
        }
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(10);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(15);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(15);

        $spreadsheet->getActiveSheet()->getStyle('A1:F' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//        dump($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="用户信息.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    //导出用户信息的excel
    public function exportexcel()
    {
        $info = model("Userbasic")->getinfo();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
            ->setCellValue('B1', '用户姓名')
            ->setCellValue('C1', '工号/学号')
            ->setCellValue('D1', '用户类型：')
            ->setCellValue('E1', '所属部门')
            ->setCellValue('F1', '职位');
        $departdict = array(0 => "普通用户", 1 => "院领导", 2 => "部门领导", 3 => "系领导");
        $spreadsheet->getActiveSheet()->setTitle('用户信息');
        //dump($info);
        $i = 2; //从第二行开始
        foreach ($info as $data) {
            $spreadsheet->getActiveSheet()
                ->setCellValue('A' . $i, $data['id'])
                ->setCellValue('B' . $i, $data['ui_name'])
                ->setCellValue('C' . $i, $data['work_id'])
                ->setCellValue('D' . $i, $departdict[$data['type_id']])
                ->setCellValue('E' . $i, $data['ud_name'])
                ->setCellValue('F' . $i, $data['up_name']);

            $i++;
        }
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('A')
            ->setWidth(10);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(15);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(20);
        $spreadsheet->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(15);

        $spreadsheet->getActiveSheet()->getStyle('A1:F' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="用户信息.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }


}