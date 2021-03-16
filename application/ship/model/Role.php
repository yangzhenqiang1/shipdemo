<?php
/**
 * Created by PhpStorm.
 * Login: qiang
 * Date: 2017-06-23
 * Time: 11:21
 */


namespace app\ship\model;
use think\Model;
class Role extends Model
{
protected $table='roles';
protected function User(){
    return $this->belongsTo('Login','RoleId');
}
}