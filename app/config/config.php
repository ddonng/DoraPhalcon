<?php

defined('APP_PATH') || define('APP_PATH', realpath('.'));

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        // 'host'        => '10.34.2.46',
        'host'        => 'localhost',
        'username'    => 'myuser',
        'password'    => '123456',
        'dbname'      => 'swoole',
        'charset'     => 'utf8',
        // 'persistent'  => true,
    ),
    'application' => array(
        'controllersDir' => APP_PATH . '/app/controllers/',
        'modelsDir'      => APP_PATH . '/app/models/',
        'migrationsDir'  => APP_PATH . '/app/migrations/',
        // 'viewsDir'       => APP_PATH . '/app/views/',
        'pluginsDir'     => APP_PATH . '/app/plugins/',
        'libraryDir'     => APP_PATH . '/app/library/',
        // 'cacheDir'       => APP_PATH . '/app/cache/',
        'baseUri'        => '/swooleDora/',
    )
));
