# DoraPhalcon

标签（空格分隔）：swoole doraRPC phalcon

---

这是一个菜鸟的好时代，因为大神们不断的贡献……好人一生平安！(●ˇ∀ˇ●)

DoraPhalcon只是将DoraRPC与Phalcon组装在一起……DoraRPC写得非常好，作者[@蓝天](http://weibo.com/thinkpc)还在继续按照他最初的完美设计在不断完成，所以[DoraRPC](https://github.com/xcl3721/Dora-RPC)还可以更好o(^▽^)o，顺便带上DoraPhalcon。用Phalcon是因为太喜欢它的DI了，而且用习惯了后也不想再换框架……要我自己写，水平不够，轮子造不出来( ▼-▼ )……

DoraPhalcon期待上PHP7，目前：
> *  PHP7已发布
> *  Swoole支持PHP7
> *  Phalcon即将支持，可能2016年上半年

------
## 依赖
这里有一堆依赖或者说Extensions（环境为Ubuntu14.10)
### 1. PHP-CLI
菜鸟装PHP7这里有个sh好用哟：
```bash
#!/bin/bash
apt-get update
apt-get install -y git-core autoconf bison libxml2-dev libbz2-dev libmcrypt-dev libcurl4-openssl-dev libltdl-dev libpng-dev libpspell-dev libreadline-dev
mkdir -p /etc/php7/conf.d
mkdir -p /etc/php7/cli/conf.d
mkdir /usr/local/php7
cd /tmp
git clone https://github.com/php/php-src.git --depth=1
cd php-src
./buildconf
./configure --prefix=/usr/local/php7 --enable-bcmath --with-bz2 --enable-calendar --enable-exif --enable-dba --enable-ftp --with-gettext --with-gd --enable-mbstring --with-mcrypt --with-mhash --enable-mysqlnd --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-openssl --enable-pcntl --with-pspell --enable-shmop --enable-soap --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-wddx --with-zlib --enable-zip --with-readline --with-curl --with-config-file-path=/etc/php7/cli --with-config-file-scan-dir=/etc/php7/cli/conf.d
make
make test
make install
```
如果是PHP5(如果是生成环境，不需要编译直接把so文件拿来)
```Bash
sudo apt-get install php5-common php5-cli php5-dev
```
### 2. Mysql client
```Bash
sudo apt-get install php5-mysqlnd
```
### 3. 安装Phalcon拓展
先安装libpcre3-dev，否则有可能在编译过程中报错fatal error: pcre.h: No such file or directory
```Bash
sudo  apt-get install libpcre3-dev
#然后安装phaclcon
git clone --depth=1 git://github.com/phalcon/cphalcon.git
cd cphalcon/build/64bits
export CFLAGS="-O2 --fvisibility=hidden" //Centos中不需要
sudo phpize
sudo ./configure --enable-phalcon
sudo make && make install
```
然后添加拓展，在terminal中php --ini查看php.ini的文件地址，修改20-pdo_mysql.ini文件，在最后加入extension=phalcon.so。注意，直接在php.ini中添加的话会出现加载顺序问题，最后这样添加。
### 4. 安装Swoole拓展
我就直接copy
```Bash
git clone https://github.com/swoole/swoole-src.git
cd swoole-src
phpize
./configure
make && make install
```
直接在php.ini中添加swoole拓展
### 5.安装redis server与client
server是本机使用来给task传递数据的，用的unix socket方式。我直接apt-get install redis-server安装，需要修改redis.conf文件，目录是/etc/redis/。将这两行注释去掉：
```vim
unixsocket /var/run/redis/redis.sock
unixsocketperm 777
```
然后再安装redis的client，用的这个phpredis https://github.com/phpredis/phpredis，phpize，configure，make等。最后在php.ini中添加redis.so

### 6.设置ulimit
ulimit -c查看一下，如果是0，执行ulimit -n 100000，如果不能修改，需要设置 /etc/security/limits.conf，加入

    * soft nofile 262140
    * hard nofile 262140
    root soft nofile 262140
    root hard nofile 262140
    * soft core unlimited
    * hard core unlimited
    root soft core unlimited
    root hard core unlimited

加入后如果还是不行，重启系统后再设置

----
##使用说明[^code]
server目录是DoraRPC的目录
###server.php
```php
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
        //包处理进程，根据情况调整数量,查看swoole文档
        'worker_num' => 500,
        
        //the number of task logical process progcessor run you business code
        //实际业务处理进程，根据需要进行调整。需要根据Mysql的max_connections设置，这个会是运行期间的连接数
        'task_worker_num' => 50, 
        //太大引起崩溃会提示onFinish那里的$data是数组而不是string, 输出的话很可能是mysql too many connections或者redis server went away
        
        //可设置为1，不过我为了调试方便设置为0，然后terminal中配合screen使用
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

                $syncRoute =  new  Phalcon\Mvc\Micro\Collection();
                // $syncRoute->setHandler(new SyncController($di));
                $syncRoute->setHandler('SyncController',true);

                $syncRoute->setPrefix('sync/');
                $syncRoute->map('get_user/{api}/{guid}','indexAction');
                $syncRoute->map('add_user/{api}/{guid}','addUserAction');
                $syncRoute->map('update_user/{api}/{guid}','updateUserAction');
                $syncRoute->map('insert_user/{api}/{guid}','insertUserAction');
                $syncRoute->map('plus_user/{api}/{guid}','plusUserAction');
                self::$appInstance->mount($syncRoute);

                $asyncRoute =  new  Phalcon\Mvc\Micro\Collection();
                // $asyncRoute->setHandler(new AsyncController($di));
                $asyncRoute->setHandler('AsyncController',true);
                $asyncRoute->setPrefix('async/');
                $asyncRoute->map('add_user/{api}/{guid}','addUserAction');
                $asyncRoute->map('update_user/{api}/{guid}','updateUserAction');
                $asyncRoute->map('insert_user/{api}/{guid}','insertUserAction');
                $asyncRoute->map('plus_user/{api}/{guid}','plusUserAction');
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
        
    }
    function doWork($param){
    
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

```
server会在每次dowork的时候初始化phalcon，其实就是new DI，然后创建好路由，转发给相应的Controller的Action进行处理。而且DI中使用的是setShared，也就是单例，有多少task_work_num就会有多少次创建。即，mysql的连接数，还有redis的连接数都会 **一直等于** Task_work_num。我都在向往一直用阿里的最小那台最大60连接数的RDS了o(^▽^)o。

-------
## 注意

 1. mysql连接会一直保持，不要尝试在业务代码里面手动close，我压测的时候试了，要出错。
 2. redis一定要在每个Action执行完后删除数据，否则数据量会越来越大，最后占用内存上去了，redis server went away
 3. 使用前请一定多跑跑测试，我水平真的很菜哟

## 测试

我在一台虚拟机中测试：虚拟机设置了i3-2100 4核，4G内存，reactor_num 8，worker_num 500,task_work_num 50，mysql最大连接60，共300万次写入全部成功。测试过程中，使用mytop查看mysql连接，连接数始终为51（1为mytop的连接），QPS为2000左右；redis连接数始终为51，内存占用为668MB（因为内容几乎一致所以没有什么波动）。时间忘了看了。cpu占用保持在80%，内存保持为2G。改天我再去阿里上分机测试下。
###client.php
```php
<?php
include "src/doraconst.php";
include "src/packet.php";
include "src/client.php";

//app server config 
$config = array(
    array("ip"=>"127.0.0.1","port"=>9567)
);
$obj = new DoraRPC\Client($config);
file_put_contents("/tmp/sw_client_test.log","start:".date("Y-m-d H:i:s")."\r\n", FILE_APPEND);
for ($i = 0; $i < 1000000; $i++) {
    //single && sync
    // $ret = $obj->singleAPI("get_user", array("nu"=>234, "name"=>$i), true,1);
    // var_dump($ret);
    
    // WARNING!!! modelsManager and new User at the same time have Problem!  ---Record Exists! ERROR! 
    // multi && async
    $data = array(
        "oak" => array("name" => "add_user", "param" => array("name" => "NO".$i,"department"=>"DEP".$i)),
        // "cd" => array("name" => "update_user", "param" => array("name" => "update".$i,"department"=>"update".$i)),
        "mmm" => array("name" => "insert_user", "param" => array("name" => "insert".$i,"department"=>"insert".$i)),
        "sdgg" => array("name" => "plus_user", "param" => array("name" => "plus".$i,"department"=>"plus".$i)),
    );
    //edit false to true to  use sync
    $ret = $obj->multiAPI($data, false, 1);
    var_dump($ret);

}
file_put_contents("/tmp/sw_client_test.log","End:".date("Y-m-d H:i:s")."\r\n", FILE_APPEND);

```
----
##鸣谢
最后，感谢各位大神们，感谢@蓝天，感谢@韩天峰，感谢全体开源界的牛牛们！

----
[^code]:注DoraRPC的monitor目前我还没开启，所以暂时没有说明