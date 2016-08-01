<?php
require_once("/home/www/vendor/autoload.php");
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
