<?php
$serv = new swoole_server("0.0.0.0", 9001, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

// 监听数据接收事件
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server" . $data);
    var_dump($clientInfo);
});

// 启动服务器 
$serv->start();