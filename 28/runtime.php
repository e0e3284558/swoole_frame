<?php

require './WaitGroup.php';

use Swoole\Http\Server;

// 默认不支持Curl
Swoole\Runtime::enableCoroutine(true, SWOOLE_HOOK_ALL | SWOOLE_HOOK_CURL);

$http = new Server("0.0.0.0", 9501);
$http->on('request', function ($request, $response) {
    $uri = $response->server['request_uri'];
    if ($uri == '/favicon.ico') {
        $response->status(404);
        $response->end();
        return;
    }
    $wait = new WaitGroup();
    $wait->add();// 每创建一个协程就创建一次
    go(function () use ($wait) {
        sleep(2);
        $wait->push(['data' => 1]);
    });

    $wait->add();// 每创建一个协程就创建一次
    go(function () use ($wait) {
        sleep(2);
        $wait->push(['data' => 2]);
    });
    $return = $wait->wait();

    var_dump($return);

    $response->end("<br> <h1>执行ok. #" . rand(1000, 9999) . "</h1>");
});


echo "http://0.0.0.0:9501 \n";
$http->start();