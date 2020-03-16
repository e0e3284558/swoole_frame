<?php

$host = '0.0.0.0';
// 创建swoole
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server($host, 9501);

//$serv->set([
//    'heartbeat_idle_time' => 10,
//    'heartbeat_check_interval' => 3,
//]);

// 注册swoole
//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    echo "接收到消息" . $data;
    $serv->send($fd, "Server: " . $data);
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close(qq离线).\n";
});


$serv->on('Start', function ($serv) use ($host) {
    echo "启动swoole，监听tcp:{$host}:9501";
});

echo "tcp:{$host}:9501\n";

//启动服务器
$serv->start();