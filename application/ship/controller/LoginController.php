<?php
/**
 * Created by PhpStorm.
 * Login: qiang
 * Date: 2017-06-23
 * Time: 10:35
 */

namespace app\ship\controller;


use app\ship\model\User;
use think\Controller;

class LoginController extends Controller
{
    function login()
    {
        $this->view->engine->layout(false);
        return view();
    }
    function checkUser(){
        $name=input('post.username');
        $pwd=input('post.password');
        $user=User::get(['LoginId'=>$name,'LoginPwd'=>$pwd]);
        if($user){
            session('user',$user);
            return $this->redirect('/shiplist');
        }else{
            return "<script>alert('用户名或密码错误');history.go(-1)</script>";
        }
    }
}