<?php

use Swoole\Async\Client;

// 异步客户端
$client = new Client(SWOOLE_SOCK_TCP);

$client->on('connect', function (Client $cli) {
    $cli->send("GET / HTTP/1.1\r\n\r\n");
});

//接收到数据
$client->on('receive', function (Client $cli, $data) {
    echo "接收到消息";
    $this->send($cli);
//    echo "\n准备生成订单: \n $data \n";
//    sleep(3);
//    $cli->send(str_repeat('A', 100) . "\n");
//    echo "\n 订单生成成功:  \n $data \n";

});

$client->on('error', function (Client $cli) {
    echo "error\n";
});

$client->on('close', function (Client $cli) {
    echo "连接关闭";
});


$client->connect('127.0.0.1', 9501);

$r = 0;
//while (true){
//    echo $r++."\n";
//    sleep(1);
//}
swoole_timer_tick(3000, function ($timer_id) use ($client) {
    echo "定时发送信息\r\n";
    $client->send(1);

});
echo "订单生成成功\n";
//sleep(4);
echo "这是异步的\n";