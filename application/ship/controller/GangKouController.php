<?php
/**
 * Created by PhpStorm.
 * Login: qiang
 * Date: 2017-06-28
 * Time: 16:11
 */

namespace app\ship\controller;


use app\ship\model\GangKou;

class GangKouController extends CommonController
{
    function addGangkou()
    {

        return view();
    }

    function GangKouExist()
    {
        $name = input('name');
        $gk = GangKou::get(['Name' => $name]);
        if ($gk) {
            return false;
        } else {
            return true;
        }
    }

    function addGangkouAction()
    {
        $g = input('post.');
        $g['Uid'] = $this->user->Id;
        $gk = GangKou::create($g);
        if ($gk) {
            $this->success('成了', url('/addgangkou'));
        } else {
            $this->error('失败');
        }
    }

    function gangkou()
    {
        return view();
    }

    function gangkouDelete()
    {
        $gks=GangKou::all();
        $this->assign('gks',$gks);
        return view();
    }
    function gangkouDeleteAction(){
        $id = input('id');
        return GangKou::destroy($id);
    }
    function searchzl()
    {
        $id = input('id');
        $gk = GangKou::get($id)->visible(['ZiLiao']);
        return $gk;
    }

    function searchGangkou()
    {
        $area = input('area');
        $gks = GangKou::all(['Area' => $area]);
        return json($gks);
    }
}