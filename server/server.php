<?php
//修改为绝对路径，推荐直接书写而不用函数获取，如define('APP_PATH', realpath('..'));
define('APP_PATH', '/home/www/');

require_once(APP_PATH . "vendor/autoload.php");

error_reporting(E_ALL);

class Server extends DoraRPC\Server {
    private static $diInstance;
    private static $appInstance;
    //all of this config for optimize performance
    //以下配置为优化服务性能用，请实际压测调试
    protected  $externalConfig = array(

       'dispatch_mode' => 3,
        //to improve the accept performance ,suggest the number of cpu X 2
        //如果想提高请求接收能力，更改这个，推荐cpu个数x2
        'reactor_num' => 2,
        //packet decode process,change by condition
        //包处理进程，根据情况调整数量
        'worker_num' => 10,
        //the number of task logical process progcessor run you business code
        //实际业务处理进程，根据需要进行调整
        /******************************************/
        'task_worker_num' => 20, //太大引起崩溃会提示onFinish那里的$data是数组而不是string, < mysql max_connections

    );
    protected $externalHttpConfig = array(
        'reactor_num' => 2,
        'worker_num' => 10,
        'task_worker_num' => 20,
        'daemonize' => 0,
    );

    public static function getDiInstance() {
        if (!self::$diInstance) {
            self::$diInstance = new Phalcon\Di\FactoryDefault();
        }
        return self::$diInstance;
    }
    public static function getAppInstance() {
        if (!self::$appInstance) {
            try {
                /**
                 * Read the configuration
                 */
                $config = include APP_PATH . "/app/config/config.php";
                /**
                 * Read auto-loader
                 */
                include APP_PATH . "/app/config/loader.php";

                /**
                 * Read services
                 */
                $di = self::getDiInstance();
                include APP_PATH . "/app/config/services.php";

                self::$appInstance = new \Phalcon\Mvc\Micro($di);

                /**
                 * Handle the Route, Sync RPC handler
                 */
                $syncRoute =  new  Phalcon\Mvc\Micro\Collection();
                $syncRoute->setHandler('IndexController',true);
                $syncRoute->setPrefix('sync/');
                $syncRoute->map('getUserById/{key}','getUserByIdAction');
                $syncRoute->map('checkUserLoginInfo/{key}','checkUserLoginInfoAction');
                $syncRoute->map('checkLoginName/{key}','checkLoginNameAction');
                $syncRoute->map('addUser/{key}','addUserAction');
                $syncRoute->map('updateUser/{key}','updateUserAction');
                $syncRoute->map('deleteUser/{key}','deleteUserAction');
                $syncRoute->map('getUsers/{key}','getUsersAction');

                self::$appInstance->mount($syncRoute);

                /**
             	 * More, just like Async RPC handler
            	 */

            } catch (\Exception $e) {
                echo $e->getMessage() . '<br>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
            }
        }
        return self::$appInstance;
    }
    function initServer($server){
        //the callback of the server init 附加服务初始化
        //such as swoole atomic table or buffer 可以放置swoole的计数器，table等

    }
    function doWork($param){
        //process you logical 业务实际处理代码仍这里
        //return the result 使用return返回处理结果

        $app = self::getAppInstance();
        $di = self::getDiInstance();

        // Two route Controller : sync and async

        $routePrefix = '';
        $type = $param['type'];
        $apiName = $param['api']['name'];
        if($type == \DoraRPC\DoraConst::SW_MODE_WAITRESULT_SINGLE || $type == \DoraRPC\DoraConst::SW_MODE_WAITRESULT_MULTI)
        {
            $routePrefix = 'sync/'.$apiName;
        } elseif($type == DoraRPC\DoraConst::SW_MODE_ASYNCRESULT_SINGLE || $type == DoraRPC\DoraConst::SW_MODE_ASYNCRESULT_MULTI){
            $routePrefix = 'async/'.$apiName;
        }

        $key = $type."_".$apiName."_".$param['guid'];
        $table = $di->get('table');
        $table->set($key,array("param"=>serialize($param['api']['param'])));

        $route = $routePrefix.'/'.$key;
        return $app->handle($route);
    }
    function initTask($server, $worker_id){
        //require_once() 你要加载的处理方法函数等 what's you want load (such as framework init)
        // self::getAppInstance();
        $app = self::getAppInstance();
        $di = self::getDiInstance();
    }
}
//this server belong which logical group
//different group different api(for Isolation)
$groupConfig = array(
    "list" => array(
        "test_group",
    ),
);
//redis for service discovery register
//when you on product env please prepare more redis to registe service for high available
$redisconfig = array(
    array(//first reporter,可改为ip
        "ip" => getenv("HOST_MACHINE_IP"),
        "port" => "6379",
    ),
);

$res = new Server("0.0.0.0", 9567, 9566, $groupConfig, $redisconfig);
