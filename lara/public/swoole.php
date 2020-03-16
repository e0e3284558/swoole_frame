<?php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

// 应用程序的初始化
$app = require_once __DIR__ . '/../bootstrap/app.php';
// 请求处理的初始化
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$http = new Swoole\Http\Server("0.0.0.0", 9501);
echo "http://0.0.0.0:9501";
$http->on('request', function ($swoole_request, $swoole_response) use ($kernel) {

    $_SERVER = [];
    if (isset($swoole_request->server)) {
        foreach ($swoole_request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    // 这个一定要写不然会报错
    $_SERVER['argv'] = [];
    if (isset($swoole_request->header)) {
        foreach ($swoole_request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    $_GET = [];
    if (isset($swoole_request->get)) {
        foreach ($swoole_request->get as $k => $v) {
            if($k == 's'){
                $_GET[$k] = $v;
            }else{
                $_GET[strtoupper($k)] = $v;
            }
        }
    }
    $_POST =[];
    if (isset($swoole_request->post)) {
        foreach ($swoole_request->post as $k => $v) {
            $_POST[strtoupper($k)] = $v;
        }
    }
// 程序处理
    $laravel_response = $kernel->handle(
        $laravel_request = Illuminate\Http\Request::capture()
    );
//    框架输出默认是echo;
//    $laravel_response->send();
    $swoole_response->end($laravel_response->getContent());
    $kernel->terminate($laravel_request, $laravel_response);

});
$http->start();


