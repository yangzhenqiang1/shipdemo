<?php

/**
 * Created by PhpStorm.
 * Login: qiang
 * Date: 2017-06-23
 * Time: 11:19
 */
namespace app\ship\model;

use think\Model;

class User extends Model
{
    protected $table = 'users';

    protected function Role()
    {
        return $this->belongsTo('Role','RoleId');
    }
}