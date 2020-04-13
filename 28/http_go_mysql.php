<?php

use Swoole\Http\Server;

$http = new Server("0.0.0.0", 9501);
$http->on('request', function ($request, $response) {
    $uri = $response->server['request_uri'];
    if ($uri == '/favicon.ico') {
        $response->status(404);
        $response->end();
        return;
    }
    $newTime = microtime(true);
    $swoole_mysql1 = new Swoole\Coroutine\MySQL();
    $swoole_mysql1->connect([
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'password' => 'bfccm@db123',
        'database' => 'swoole',
    ]);
//    $res = $swoole_mysql->query('select count(*) from dept');
    $swoole_mysql1->setDefer(); // 延迟收报
    $res1 = $swoole_mysql1->query('select sleep(2)');
    $swoole_mysql1->recv();

    $swoole_mysql2 = new Swoole\Coroutine\MySQL();
    $swoole_mysql2->connect([
        'host' => 'localhost',
        'port' => 3306,
        'user' => 'root',
        'password' => 'bfccm@db123',
        'database' => 'swoole',
    ]);
    $swoole_mysql1->setDefer(); // 延迟收报

    $res2 = $swoole_mysql2->query('select sleep(2)');
    $swoole_mysql2->recv();


    echo microtime(true) - $newTime;
    var_dump($swoole_mysql1->recv(), $swoole_mysql2->recv());

    $response->end("<br> <h1>执行ok. #" . rand(1000, 9999) . "</h1>");
});


echo "http://0.0.0.0:9501 \n";
$http->start();