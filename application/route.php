<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]' => [
        ':id' => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    'login'=>['ship/Login/checkUser',['method'=>'post']],
    'shiplist'=>['ship/Ship/shiplist',['method'=>'get']],
    'changeship'=>'ship/Ship/changeShip',
    'deleteship'=>'ship/Ship/deleteShip',
    'shipdetails/:id'=>'ship/Ship/shipDetails',
    'editship/:id'=>'ship/Ship/editShip',
    'modifyShipAction'=>['ship/Ship/modifyShipAction',['method'=>'post']],
    'exportExcel'=> 'ship/Ship/exportExcel',
    'addship'=>'ship/Ship/addShip',
    'shipexist'=>'ship/Ship/shipExist',
    'addshipaction'=>'ship/Ship/addShipAction',
    'importShip' => 'ship/Ship/importShip',
    'insertexcel' => 'ship/Ship/insertexcel',
    //港口
    'addgangkou'=>'ship/GangKou/addGangkou',
    'addgangkouaction'=>'ship/GangKou/addGangkouAction',
    'gangkouexist'=>'ship/GangKou/gangkouExist',
    'gangkou'=>'ship/GangKou/gangkou',
    'gangkoudelete'=>'ship/GangKou/gangkouDelete',
    'gangkoudeleteaction'=>'ship/GangKou/gangkouDeleteAction',
    'searchgangkou'=>'ship/GangKou/searchGangkou',
    'searchzl'=>'ship/GangKou/searchzl',
    //用户
    'usermanager'=>'ship/User/userManager',
    'adduser'=>'ship/User/addUser',
    'userExists'=>'ship/User/userExists',
    'editUser'=>'ship/User/editUser',
    'deleteUser' => 'ship/User/deleteUser',
    'usereditor' => 'ship/User/userEditor',
    'usereditoraction'=>'ship/User/userEditorAction',
    'changepwd'=>'ship/User/changepwd',
    'changepwdaction'=>'ship/User/changepwdAction',
    'exit'=>'ship/User/exitUser',
    //备份
    'backup'=>'ship/Common/backup',
    'backupAction' => 'ship/Common/backupAction',
];
