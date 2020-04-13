<?php
require './WaitGroup.php';

use Swoole\Http\Server;


//$chan = new chan(1);
//
//go(function () use ($chan) {
//    // 协程空间
//    $chan->push('token');
//});
//
//go(function () use ($chan) {
//    // 协程空间
//    echo $chan->pop();
//});


$http = new Server("0.0.0.0", 9501);
$http->on('request', function ($request, $response) {
    $uri = $response->server['request_uri'];
    if ($uri == '/favicon.ico') {
        $response->status(404);
        $response->end();
        return;
    }
    $newTime = microtime(true);
//    $chan = new chan(2);
    $wait=new WaitGroup();
    $wait->add();// 每创建一个协程就创建一次
    go(function () use ($wait) {
        $swoole_mysql1 = new Swoole\Coroutine\MySQL();
        $swoole_mysql1->connect([
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'root',
            'password' => 'bfccm@db123',
            'database' => 'swoole',
        ]);
        $swoole_mysql1->setDefer(); // 延迟收报
//        $res1 = $swoole_mysql1->query('select sleep(2)');
        $res = $swoole_mysql1->query('select count(*) from dept');

        $wait->push($swoole_mysql1->recv());
    });

    $wait->add();// 每创建一个协程就创建一次
    go(function () use ($wait) {
        $swoole_mysql2 = new Swoole\Coroutine\MySQL();
        $swoole_mysql2->connect([
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'root',
            'password' => 'bfccm@db123',
            'database' => 'swoole',
        ]);
        $swoole_mysql2->setDefer(); // 延迟收报

        $res2 = $swoole_mysql2->query('select sleep(2)');
//        $res = $swoole_mysql2->query('select count(*) from dept');

        $wait->push($swoole_mysql2->recv());
    });
    $return=$wait->wait();

    var_dump($return);
    echo microtime(true) - $newTime;

    $response->end("<br> <h1>执行ok. #" . rand(1000, 9999) . "</h1>");
});


echo "http://0.0.0.0:9501 \n";
$http->start();