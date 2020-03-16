<?php
require __DIR__ . '/../../vendor/autoload.php';

use Bifei\Io\Multi\Worker;

$host = 'tcp://0.0.0.0:9000';
$server = new Worker($host);
$server->onConnect = function ($socket, $client) {
    echo "有一连接进来了  \n";
//    var_dump($client);
};
// 接收和处理信息
$server->onReceive = function ($socket, $client, $data) {
    echo "给连接发送信息 \n";
//    sleep(3);
    $socket->send($client, "hello world client \n");
//    fwrite($client, 'server hello world');
};

//echo $host."\n";
$server->start();