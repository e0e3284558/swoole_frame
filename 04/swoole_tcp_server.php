<?php

$host = '0.0.0.0';
$port = 9501;
// 创建swoole
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server($host, $port);
//
$serv->set([
    'open_length_check' => true,
    'package_max_length' => 81920,
    // 类型
    'package_length_type' => 'N',
    // 数据从0开始
    'package_length_offset' => 0,
    // 4 是因为我们选择的pack的类型N是4位
    'package_body_offset' => 4
]);

// 注册swoole
//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});


//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    $pack = unpack('N', $data);
    $content = $pack[1];
    var_dump($content);
    var_dump(substr($data, 4, $content));
//    var_dump(explode("\r\n", $data));
//    echo "接收到消息" . $data;
//    echo "接收到{$fd}消息\n" ;
    $serv->send($fd, "Server:  " . $data);
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close(qq离线).\n";
});


$serv->on('Start', function ($serv) use ($host, $port) {
    echo "启动swoole，监听tcp:{$host}:{$port}";
});

echo "tcp:{$host}:{$port}\n";

//启动服务器
$serv->start();