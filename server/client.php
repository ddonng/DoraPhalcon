<?php
include "src/doraconst.php";
include "src/packet.php";
include "src/client.php";

//app server config 
$config = array(
    array("ip"=>"127.0.0.1","port"=>9567),
    //array("ip"=>"127.0.0.1","port"=>9567), you can set more ,the client will random select one,to increase High availability
);

$obj = new DoraRPC\Client($config);
file_put_contents("/tmp/sw_client_test.log","start:".date("Y-m-d H:i:s")."\r\n", FILE_APPEND);
for ($i = 0; $i < 1000; $i++) {
    //single && sync
    $ret = $obj->singleAPI("abc", array(234, $i), true,1);
    var_dump($ret);

    //multi && async
    // $data = array(
    //     "oak" => array("name" => "oakdf", "param" => array("dsaf" => "321321")),
    //     "cd" => array("name" => "oakdfff", "param" => array("codo" => "fds")),
    // );
    // $ret = $obj->multiAPI($data, true,1);
    // var_dump($ret);
}
file_put_contents("/tmp/sw_client_test.log","End:".date("Y-m-d H:i:s")."\r\n", FILE_APPEND);

