<?php
/**
 * Created by PhpStorm.
 * Login: qiang
 * Date: 2017-06-26
 * Time: 8:42
 */

namespace app\ship\controller;

use app\ship\model\Ship;
use app\ship\model\User;
use think\Controller;

class ShipController extends CommonController
{
    function shiplist()
    {
        $ship = new Ship();
        $ships = $ship->where('1=1');
        if ($this->user->RoleId != 1) {
            $ships = $ships->where('Uid=' . $this->user->Id);
        }
        if (input('get.ShipName')) {
            $ShipName = input('get.ShipName');
            $ships = $ships->where("ShipName like '%$ShipName%'");
        }
        if (input('get.CompanyName')) {
            $CompanyName = input('get.CompanyName');
            $ships = $ships->where("CompanyName  like '%$CompanyName%'");
        }
        if (input('get.xiao') && input('get.da')) {
            $da = input('get.da');
            $xiao = input('get.xiao');
            $ships = $ships->where("ZaiZhongDun<$da and ZaiZhongDun>$xiao");
        }

        if (!input('get.xiao') && input('get.da')) {
            $da = input('get.da');
            $ships = $ships->where("ZaiZhongDun<$da");
        }
        if (input('get.xiao') && !input('get.da')) {
            $xiao = input('get.xiao');
            $ships = $ships->where("ZaiZhongDun>$xiao");
        }
        if (input('get.Contact')) {
            $contact = input('get.Contact');
            $ships = $ships->where("(Contact1='$contact' or Contact2='$contact' or Contact3='$contact')");
        }
        $ships = $ships->paginate(20,false,['query'=>input('get.')]);
        $res = json_encode($ships);
        session('ships', $res);
        $a = json_decode($res, true);
//        dump($a);
        $this->assign('a', $a);
        $this->assign('ships', $ships);
        return view();
    }

    function changeShip()
    {
//        $id = input('id');
//        $ship['Id'] = $id;
//        if (input('date')) {
//            $chuanqi = input('date');
//            $ship['ChuanQi'] = $chuanqi;
//        }
//        if (input('mark')) {
//            $chuanqi = input('mark');
//            $ship['Mark'] = $chuanqi;
//        }
//        Ship::update($ship);
        $id = input('id');
        $ship['Id'] = $id;
        $ship['ChuanQi'] = input('date');
        $ship['Mark'] = input('mark');
        Ship::update($ship);
        $ships = json_decode(session('ships'), true);
        for ($i = 0; $i < count($ships['data']); $i++) {
            if ($ships['data'][$i]["Id"] == $id) {
                $ships['data'][$i]["ChuanQi"] = $ship['ChuanQi'];
                $ships['data'][$i]["Mark"] = $ship['Mark'];
                break;
            }
        }
        session('ships', json_encode($ships));
    }


    function deleteShip()
    {
        $id = input('id');
        Ship::destroy($id);
    }

    function shipDetails()
    {
        $id = request()->route('id');
        $ship = Ship::get($id);
        $this->assign('ship', $ship);
        return view();
    }

    function editShip()
    {
        $id = request()->route('id');
        $ship = Ship::get($id);
        $this->assign('ship', $ship);
        return view();
    }

    function modifyShipAction()
    {
//        dump(input('post.'));
        Ship::update(input('post.'));
    }

    /*导出Excel文件*/
    function exportExcel()
    {
        //需要修改的地方用-----标注
        //-------------
        $jsonData = session('ships'); //获取需要生成excel的数据
        //------------
        $rand = microtime();//定义文件名
        //---------------
        $filePath = ROOT_PATH . 'public' . DS . 'uploads/' . $rand . '.xlsx';//存放目录+文件
        //--------------
        $tabelJsonHeader = json_encode(array(//定义excel文件头部
            0 => "Id",//自增索引，用于根据此键获取对应的数据库值，数据库的键或者重命名的键（字段）
            "T0" => "编号",//设置表头值99999+自增索引
            1 => "CompanyName",
            "T1" => "公司名称",
            2 => "ShipName",
            "T2" => "船名",
            3 => "ZaiZhongDun",
            "T3" => "载重吨",
            4 => "ChuanQi",
            "T4" => "船期",
            5 => "Contact1",
            "T5" => "联系人1",
            6 => "Tel1",
            "T6" => "电话",
            7 => "Contact2",
            "T7" => "联系人2",
            8 => "Tel2",
            "T8" => "电话",
            9 => "Contact3",
            "T9" => "联系人3",
            10 => "Tel3",
            "T10" => "电话",
            11 => "CangkouShu",
            "T11" => "舱口数",
            12 => "ZongCangRong",
            "T12" => "总仓容",
            13 => "ManzaiChishui",
            "T13" => "满载吃水",
            14 => "ZongDun",
            "T14" => "总吨",
            15 => "JingDun",
            "T15" => "净吨",
            16 => "Length",
            "T16" => "船长",
            17 => "Width",
            "T17" => "船宽",
            18 => "Mark",
            "T18" => "备注"
        ));
        //-------------------
        $colDataType = array(
            "Tel1" => \PHPExcel_Cell_DataType::TYPE_STRING,//设置某个单元格格式
            "Tel2" => \PHPExcel_Cell_DataType::TYPE_STRING,//设置某个单元格格式
            "Tel3" => \PHPExcel_Cell_DataType::TYPE_STRING,//设置某个单元格格式
            "ChuanQi" => \PHPExcel_Cell_DataType::TYPE_STRING//设置某个单元格格式
        );
        $url = $this->createEXCEL($filePath, $jsonData, $tabelJsonHeader, $colDataType);//在PHPExcel.php中配置PHPExcel文件包含路径

        if ($url) {//此url如果为空说明失败，成功，则直接跳转此路径即可
            //---------
            $this->redirect("/uploads/$rand.xlsx");
        }

    }

    //生成excel
    function createEXCEL($filePath, $jsonData, $tableJsonHeader, $colDataType = null)
    {
        $objPHPExcel = new \PHPExcel();
        $sheet = $objPHPExcel->getSheet(0);
        $dataArr = json_decode($jsonData, true);
        if (empty($dataArr)) {
            return "";
        }
        $tableHeaderArr = json_decode($tableJsonHeader, true);
        $c = count($dataArr['data']);//由于获取的数据是对象数组格式，其中data属性是所需值,按实际情况修改
        $l = count($tableHeaderArr) / 2;//数字索引和关联索引实际是表示一个值
        $AZ = $this->setAZ(19);//总共有的列数
        for ($m = 0; $m < $l; $m++) {//表头
            $A = $AZ[$m] . "1";
            $sheet->setCellValue($A, $tableHeaderArr["T" . $m]);//
        }
        for ($i = 0; $i < $c; $i++) {//行
            for ($m = 0; $m < $l; $m++) {//列
                //$sheet ->setCellValue($AZ[$m].($i+2), $dataArr[$i][$tableHeaderArr[$m]]);//会将00002转换成2
                if (array_key_exists($tableHeaderArr[$m], $colDataType))
                    $sheet->setCellValueExplicit($AZ[$m] . ($i + 2), $dataArr['data'][$i][$tableHeaderArr[$m]], $colDataType[$tableHeaderArr[$m]]);//某些单元格设置设置格式
                else
                    $sheet->setCellValue($AZ[$m] . ($i + 2), $dataArr['data'][$i][$tableHeaderArr[$m]]);//默认格式
            }
        }
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($filePath);
        return $filePath;
    }

//暂时使用此方法，没有找到更简便的生成A-Z..的方式
    function setAZ($n = 0)
    {
        $AZ = array(
            0 => "A",
            1 => "B",
            2 => "C",
            3 => "D",
            4 => "E",
            5 => "F",
            6 => "G",
            7 => "H",
            8 => "I",
            9 => "J",
            10 => "K",
            11 => "L",
            12 => "M",
            13 => "N",
            14 => "O",
            15 => "P",
            16 => "Q",
            17 => "R",
            18 => "S",
            19 => "T",
            20 => "U",
            21 => "V",
            22 => "W",
            23 => "X",
            24 => "Y",
            25 => "Z"
        );
        $arr = array();
        $m = 0;
        $mm = 0;
        for ($i = 0; $i < $n; $i++) {
            if (0 <= $i && $i < 26) {
                $arr[$i] = $AZ[$i];////26个字母轮循1次
            } else if (26 <= $i && $i < 42) {
                $arr[$i] = "A" . $AZ[$m];//26个字母轮循2次
                $m++;
            } else if (42 <= $i && $i < 68) {
                $arr[$i] = "B" . $AZ[$mm];//26个字母轮循3次
                $mm++;
            }//下面依次类推
        }
        return $arr;
    }
    /*导出Excel文件结束*/
    /*导入Excel*/
    function importShip(){
        return view();
    }
    function insertExcel()
    {
        $this->assign('existShip', '');
        $this->assign('msg', '');
        $this->assign('ex', '');
        try {
            $file = request()->file('upfile');
            if (!empty($file)) {
                $existShip = array();//设置已存在的数据
                $i = 0;//记录导入的数据量
                $time = date("y-m-d-H-i-s"); // 去当前上传的时间 // 获取上传文件的扩展名
                $info = $file->validate(['ext' => 'xls,xlsx'])->move(ROOT_PATH . 'public' . DS . 'uploads', $time);
                $uploadfile = $info->getRealPath(); // 上传后的文件地址
                $objReader = \PHPExcel_IOFactory::createReader('excel2007'); //use Excel5 for 2003 format
                $excelpath = $uploadfile;//上传文件路径
                $objPHPExcel = $objReader->load($excelpath);
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();           //取得总行数
                $highestColumn = $sheet->getHighestColumn(); //取得总列数
                for ($j = 2; $j <= $highestRow; $j++)                        //从第二行开始读取数据
                {
                    $str = "";
                    for ($k = 'A'; $k <= $highestColumn; $k++)            //从A列读取数据
                    {
                        $str .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue() . '|*|';//读取单元格
                    }
                    $str = mb_convert_encoding($str, 'utf8', 'auto');//根据自己编码修改
                    $strs = explode("|*|", $str);
                    $shipName = $strs[2];

                    $count = Ship::get(["ShipName" => $shipName]);
                    //将船舶信息添加到数组
                    $arr = [
                        'Id' => null,
                        'CompanyName' => $strs[1],
                        'ShipName' => $strs[2],
                        'ZaiZhongDun' => $strs[3],
                        'ChuanQi' => $strs[4],
                        'Contact1' => $strs[5],
                        'Tel1' => $strs[6],
                        'Contact2' => $strs[7],
                        'Tel2' => $strs[8],
                        'Contact3' => $strs[9],
                        'Tel3' => $strs[10],
                        'CangkouShu' => $strs[11],
                        'ZongCangRong' => $strs[12],
                        'ManzaiChishui' => $strs[13],
                        'ZongDun' => $strs[14],
                        'JingDun' => $strs[15],
                        'Length' => $strs[16],
                        'Width' => $strs[17],
                        'Mark' => $strs[18],
                        'Uid' => $this->user->Id
                    ];
                    if (!$count) {
                        $c = Ship::create($arr);
                        $i += 1;

                    } else {
                        //数据在数据库中存在
                        $existShip[] = $arr;
                    }
                }
                $this->assign('c', $i);
                if (count($existShip) > 0) {
                    $this->assign('existShip', $existShip);
                }
            } else {
                $this->assign('msg', '请选择文件');
            }
        } catch (Exception $ex) {
            $this->assign('ex', $ex->getMessage());
        }
        @unlink($uploadfile);
        return view();

    }
    function addShip(){
        return view();
    }
    function shipExist(){
        $name=input('name');
        $ship=Ship::get(['ShipName'=>$name]);
        if($ship){
            return false;
        }else{
            return true;
        }
    }
    function addShipAction(){
        $res=Ship::create(input('post.'));
        if($res){
            $this->success('成了',url('/shiplist'));
        }
        else{
            $this->error('失败');
        }
    }
}