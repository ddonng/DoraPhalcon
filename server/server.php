<?php
include "src/doraconst.php";
include "src/packet.php";
include "src/server.php";

error_reporting(E_ALL);
define('APP_PATH', realpath('..'));

class Server extends DoraRPC\Server {

    private static $diInstance;
    private static $appInstance;
    //all of this config for optimize performance
    //以下配置为优化服务性能用，请实际压测调试
    protected  $externalConfig = array(
	   
       'dispatch_mode' => 3,
        //to improve the accept performance ,suggest the number of cpu X 2
        //如果想提高请求接收能力，更改这个，推荐cpu个数x2
        'reactor_num' => 8,

        //packet decode process,change by condition
        //包处理进程，根据情况调整数量
        'worker_num' => 20,

        //the number of task logical process progcessor run you business code
        //实际业务处理进程，根据需要进行调整

        /******************************************/
        'task_worker_num' => 140, //太大引起崩溃会提示onFinish那里的$data是数组而不是string
        /*******************************************/

        'daemonize' => 0, //production 1
   );

    public static function getDiInstance() {
        if (!self::$diInstance) {
            self::$diInstance = new Phalcon\Di\FactoryDefault();
            file_put_contents("/tmp/sw_server_instance.log","new DiInstance".date("Y-m-d H:i:s")."\r\n", FILE_APPEND);
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

                /**
                 * Handle the Route
                 */
                
                self::$appInstance = new \Phalcon\Mvc\Micro($di);

                $syncRoute =  new  Phalcon\Mvc\Micro\Collection();
                // $syncRoute->setHandler(new SyncController($di));
                $syncRoute->setHandler('SyncController',true);

                $syncRoute->setPrefix('sync/');
                $syncRoute->map('get_user/{api}/{guid}','indexAction');
                self::$appInstance->mount($syncRoute);

                $asyncRoute =  new  Phalcon\Mvc\Micro\Collection();
                // $asyncRoute->setHandler(new AsyncController($di));
                $asyncRoute->setHandler('AsyncController',true);
                $asyncRoute->setPrefix('async/');
                $asyncRoute->map('add_user/{api}/{guid}','addUserAction');
                $asyncRoute->map('update_user/{api}/{guid}','updateUserAction');
                self::$appInstance->mount($asyncRoute);

                file_put_contents("/tmp/sw_server_instance.log","new AppInstance".date("Y-m-d H:i:s")."\r\n", FILE_APPEND);

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

        //I need save array in memory, but swoole_table seems can't support, so I have to use Yac instead, By ddonngHuang 2015-12-29
        //yac key max 48, guid 32, I can use MAX 16 characters to name RPC, eg update_projects_names_with_pid

        self::getAppInstance();

    }
    function doWork($param){
        //process you logical 业务实际处理代码仍这里
        //return the result 使用return返回处理结果
        
        // array (
        //   'type' => 'SSS',
        //   'guid' => 'b3c29d615fc233a8780f5160bf3e059b',
        //   'fd' => 1,
        //   'api' => 
        //   array (
        //     'name' => 'abc',
        //     'param' => 
        //     array (
        //       0 => 234,
        //       1 => 99,
        //       ),
        //     ),
        //   )

        $app = self::getAppInstance();
        $di = self::getDiInstance();

        // Two route Controller : sync and async
        $routePrefix = '';
        $type = $param['type'];
        $apiName = $param['api']['name'];

        if($type == DoraRPC\DoraConst::SW_SYNC_SINGLE || $type == DoraRPC\DoraConst::SW_SYNC_MULTI)
        {
            $routePrefix = 'sync/'.$apiName;
        } elseif($type == DoraRPC\DoraConst::SW_ASYNC_SINGLE || $type == DoraRPC\DoraConst::SW_ASYNC_MULTI){
            $routePrefix = 'async/'.$apiName;
        }
        $api = $type."_".$apiName;
        $guid = $param['guid'];
        $route = $routePrefix.'/'.$api.'/'.$guid;


        // use prefix special every Interface ,prefix maxLength?

        // return $param;
        // $yacPrefix = $type."_".$apiName;

        // $key = $param['guid'];

        // $yac = new Yac($yacPrefix);

        // // Wrong-----ttl set 1 seconds. Attension Please!!! IF server quit/restart, All Yac cache will flush !!
        // // I'm so stupid, Waht time Task would be called is unknown, Just delete the cache after doAction would be fine!!
        // // $yac->set($key,$param['api'],1);//wrong, save it for remember! 2015-12-30 shurufa


        // $yac->set($key,$param['api']);
        

        $redis = $di->get('redis');
        //compare to json_encode, serialize here is better, just i feel. Otherwise, json_decode array may be a object, so ugly.
        $redis->hSet($api,$guid,serialize($param['api']));

        // var_dump($app);
        return $app->handle($route);

    }

    function initTask($server, $worker_id){
        //require_once() 你要加载的处理方法函数等 what's you want load (such as framework init)
        // self::getAppInstance();
    }
}

$res = new Server();
