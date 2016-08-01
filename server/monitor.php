<?php
//修改为绝对路径，推荐直接书写而不用函数获取，如define('APP_PATH', realpath('..'));
define('APP_PATH', '/home/www/');
require_once(APP_PATH . "vendor/autoload.php");

$Config = array(
    "Discovery" => array(
        array(//first reporter
            "ip" => getenv("HOST_MACHINE_IP"),
            "port" => "6379",
        ),
    ),
    "Config" =>"./client.conf.php"
);
$res = new \DoraRPC\Monitor("0.0.0.0", 9569, $Config);
