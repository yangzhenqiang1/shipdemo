<?php
/**
 * Created by PhpStorm.
 * User: qiang
 * Date: 2017-06-29
 * Time: 14:53
 */

namespace app\ship\controller;


use app\ship\model\User;
use think\Session;
class UserController extends CommonController
{
    function userManager()
    {
        $users=User::all();
        $this->assign('users',$users);
        return view();
    }
    function addUser(){
        User::create(input('post.'));
        $this->redirect(url('/usermanager'));
    }
    function userExists(){
        $name=input('LoginId');
        $user=User::get(['LoginId'=>$name]);
        if($user){
            return false;
        }else{
            return true;
        }
    }
    function deleteUser()
    {
        $id = input('id');
        return User::destroy($id);
    }
    function editUser(){
        $user['Id'] = input('id');
        $user['Name'] = input('name');
        $user['Tel'] = input('tel');
        User::update($user);
    }
    function userEditor(){
        $this->assign('user',$this->user);
        return view();
    }
    function userEditorAction(){
        $user = input('post.');
        $user['Id'] = $this->user->Id;

        User::update($user);
        session('user', User::get($this->user->Id));
        $this->success('修改完成', url('/usereditor'), null, 1);
    }
    function changepwd(){
        return view();
    }
    function changepwdAction(){
        $old = input('post.old');
        $new = input('post.new');
        if ($this->user->LoginPwd == $old) {
            $user['Id'] = $this->user->Id;
            $user['LoginPwd'] = $new;
            $c = User::update($user);
            $this->success('密码修改完成,请重新登陆', url('/'), null, 1);
        } else {
            return '<script>history.go(-1);alert("原始密码不正确");</script>';
        }
    }
    function exitUser()
    {
        Session::destroy();
        $this->redirect(url('/'));
    }
}