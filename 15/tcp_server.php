<?php
$host = '0.0.0.0';
$server = new Swoole\Server($host, 9000);
$r = 1;
// 注册事件
$server->on('Start', function ($server) use ($host) {
    echo "启动swoole 监听的信息tcp:{$host}:9000\n";
});
// 监听链接事件
$server->on('Connect', function ($server, $fd) {
    require_once 'index.php';
    global $obj;
    $obj = new Index();
    $obj->r++;
    echo "Client:Connect.\n";
});
// 监听数据接收时间
$server->on('Receive', function ($server, $fd, $from_id, $data) {
    global $obj;
    var_dump($obj->r);
    $server->send($fd, "Server:" . $data);
});

// 监听链接关闭事件
$server->on('Close', function ($server, $fd) {
    echo "qq离线\n";
});

$server->start();