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
        'task_worker_num' => 200,
        
        'daemonize' => 0, 
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

                $route =  new  Phalcon\Mvc\Micro\Collection();
                $route->setHandler('IndexController',true);
                $route->setPrefix('one/');
                $route->map('{guid}/{tid}','indexAction');
                self::$appInstance->mount($route);
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

        // use prefix special every Interface ,prefix maxLength?


        // return $param;
        $yac = new Yac();
        $yac->set($param['guid'],$param,1);
        $app = self::getAppInstance();

        return $app->handle("one/".$param['guid']."/tttid");
        // return array("hehe"=>"ohyes","time"=>date('H:i:s',time()));
    }

    function initTask($server, $worker_id){
        //require_once() 你要加载的处理方法函数等 what's you want load (such as framework init)
        // self::getAppInstance();
    }
}

$res = new Server();
