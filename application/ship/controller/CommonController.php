<?php
/**
 * Created by PhpStorm.
 * Login: qiang
 * Date: 2017-06-26
 * Time: 8:52
 */

namespace app\ship\controller;

use app\ship\model\Path;
use think\Controller;
use think\Request;
use think\Db;

class CommonController extends Controller
{
    protected $user ;
    public function _initialize()
    {
        $this->view->replace(['__PUBLIC__' => '/static',]);
        if (session('?user')) {
            $this->user = session('user');
            $menu=$this->user->Role->Menus;
            $path =new Path();
            $paths=$path->where('Id','in',$menu)->select();
            $this->assign('paths', $paths);
        } else {
            $this->redirect('/Login');
        }
    }
    function backup()
    {
        $dir = ROOT_PATH . 'public' . DS . 'backupsql/';
        $files = array();

        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $files[] = $file;
                }
                closedir($dh);
            }
        }
        unset($files[0], $files[1]);
        $this->assign('files', $files);
        return view();
    }

    function backupAction()
    {
        $to_file_name = ROOT_PATH . 'public' . DS . 'backupsql/' . date('YmdHis') . ".sql";
        Db::query('set names utf8');
//数据库中有哪些表
        $tabList = Db::query('show tables');
        echo "运行中，请耐心等待...<br/>";
        $info = "-- ----------------------------\r\n";
        $info .= "-- 日期：" . date("Y-m-d H:i:s", time()) . "\r\n";
        $info .= "-- 本程序不适合处理超大量数据\r\n";
        $info .= "-- ----------------------------\r\n\r\n";
        file_put_contents($to_file_name, $info, FILE_APPEND);
//将每个表的表结构导出到文件
        foreach ($tabList as $val) {
            $sql = "show create table " . $val["Tables_in_ship"];
            $row = Db::query($sql);
            // dump(json_decode(json_encode($row), true));
            $info = "-- ----------------------------\r\n";
            $info .= "-- Table structure for `" . $val["Tables_in_ship"] . "`\r\n";
            $info .= "-- ----------------------------\r\n";
            $info .= "DROP TABLE IF EXISTS `" . $val["Tables_in_ship"] . "`;\r\n";
            $sqlStr = $info . $row[0]["Create Table"] . ";\r\n\r\n";
            //追加到文件
            file_put_contents($to_file_name, $sqlStr, FILE_APPEND);
            //释放资源
        }
//将每个表的数据导出到文件
        foreach ($tabList as $val) {
            $sql = "select * from " . $val["Tables_in_ship"];
            $res = Db::query($sql);
            //如果表中没有数据，则继续下一张表
            if (count($res) < 1) continue;
            //
            $info = "-- ----------------------------\r\n";
            $info .= "-- Records for `" . $val["Tables_in_ship"] . "`\r\n";
            $info .= "-- ----------------------------\r\n";
            file_put_contents($to_file_name, $info, FILE_APPEND);
            //读取数据
            foreach ($res as $r) {
                $sqlStr = "INSERT INTO `" . $val["Tables_in_ship"] . "` VALUES (";
                foreach ($r as $zd) {
                    $sqlStr .= "'" . $zd . "', ";
                }
                //去掉最后一个逗号和空格
                $sqlStr = substr($sqlStr, 0, strlen($sqlStr) - 2);
                $sqlStr .= ");\r\n";
                file_put_contents($to_file_name, $sqlStr, FILE_APPEND);
            }
            file_put_contents($to_file_name, "\r\n", FILE_APPEND);
        }
        $this->redirect(url('/backup'));
    }
}