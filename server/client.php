<?php
require_once("/home/www/vendor/autoload.php");
// $config = array(
//     "group1" => array(
//         array("ip" => "172.23.0.4", "port" => 9567),
//         //array("ip"=>"127.0.0.1","port"=>9567), you can set more ,the client will random select one,to increase High availability
//     ),
// );
//or
$config = include("client.conf.php");
// var_dump($config);
//define the mode
$mode = array("type" => 1, "group" => "test_group");
$maxrequest = 0;
//new obj
$obj = new \DoraRPC\Client($config);
//change connect mode
$obj->changeMode($mode);
// for ($i = 0; $i < 10000; $i++) {
    // $ret2 = $obj->singleAPI("getUserById", array("user_id" => 1, "foo" => $i), \DoraRPC\DoraConst::SW_MODE_WAITRESULT, 1);
    // $ret2 = $obj->singleAPI("checkUserLoginInfo", array("login_type" => "stuff_id", "login_name" => "00167","password"=>"5f9a9917d364bdb3fa7f61a5a719b694"), \DoraRPC\DoraConst::SW_MODE_WAITRESULT, 1);
    // $ret2 = $obj->singleAPI("addUser",
    //     array("user" => array(
    //         "stuff_id"=>"00189",
    //         "name" => "李四",
    //         "institution_id"=>3,
    //         "register_time"=>time(),
    //         "email"=>"ssd@11.com",
    //         "passwd"=>"5f9a9917d364bdb3fa7f61a5a719b694")),
    //      \DoraRPC\DoraConst::SW_MODE_WAITRESULT, 1);
    // $ret2 = $obj->singleAPI("updateUser",
    //     array("user" => array(
    //         "user_id"=>1,
    //         "stuff_id"=>"00180",
    //         "name" => "问道",
    //         "institution_id"=>3,
    //         "register_time"=>time(),
    //         "email"=>"adfsdf@qq.com",
    //         "passwd"=>"5f9a9917d364bdb3fa7f61a5a719b694")),
    //      \DoraRPC\DoraConst::SW_MODE_WAITRESULT, 1);
    // $ret2 = $obj->singleAPI("deleteUser",
    //     array("user_id" => 9),\DoraRPC\DoraConst::SW_MODE_WAITRESULT, 1);
    // var_dump("single sync", $ret);
    $ret2 = $obj->singleAPI("getUsers",
        array("fields"=>array("institution_id" => 3,"degree"=>"硕士")),\DoraRPC\DoraConst::SW_MODE_WAITRESULT, 1);
    // var_dump("single sync", $ret);
    var_dump("single sync", $ret2);
// }
