<?php
require __DIR__ . '/../../../../vendor/autoload.php';

use Bifei\Io\Reactor\Swoole\MulitPro\Worker;

$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);
$server->onReceive = function ($socket, $client, $data) {
//    include 'index.php';
    debug($data);
    send($client, "hello world client \n");
};
debug($host);
$server->start();

//
//$host = '0.0.0.0';
//$serv = new Swoole\Server($host, 9000);
//
//// 注册swoole
////监听连接进入事件
//$serv->on('Connect', function ($serv, $fd) {
//    echo "Client: Connect.\n";
//});
//
////监听数据接收事件
//$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
////    include "index.php";
//    (new \Bifei\Io\Index())->index();
//    $serv->send($fd, "Server: " . $data);
//});
//
//
////启动服务器
//$serv->start();