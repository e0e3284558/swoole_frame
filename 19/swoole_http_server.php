<?php
$http = new Swoole\Http\Server("0.0.0.0", 9000);
$http->set([
    'worker_num' => 1,
]);
// 会返回一个监听的服务
$listen = $http->addListener('127.0.0.1', '8000', $type = SWOOLE_SOCK_TCP);
$listen->set([
    'worker_num' => 1,
]);

$http->on('request', function ($request, $response) {
    $response->end("<h1>Hello Swoole.#" . rand(1000, 9999) . "</h1>");
});

$http->on('receive', function ($server, $fd, $reactor_id, $data) {
    echo "超管查房\n";
    // 停止服务端运行
    // 设置用户状态
    $server->send($fd, "Server:" . $data);
});

echo "http://106.13.78.8:9000";
$http->start();
