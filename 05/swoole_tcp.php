<?php

$host = '0.0.0.0';
$port = 9501;
// 创建swoole
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server($host, $port);
//

// 注册swoole
$serv->on('Start', function () {
    // 修改进程名称
    swoole_set_process_name("swoole:start");
    echo "===> on start \n";
});

$serv->on('workerStart', function () {
    // 修改进程名称
    swoole_set_process_name("swoole:workerStart");
    echo "===> on workerStart \n";

});


//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});


//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server:");
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close(qq离线).\n";
});


echo "tcp:{$host}:{$port}\n";

//启动服务器
$serv->start();