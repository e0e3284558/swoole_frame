<?php
$client = new swoole_client(SWOOLE_SOCK_TCP);

//连接到服务器
if (!$client->connect('127.0.0.1', 8000, 0.5)) {
    die("connect failed.");
}

$client->send(1);

//从服务器接收数据
$data = $client->recv();
if (!$data) {
    die("recv failed.");
}
//关闭连接
$client->close();

echo "<br>同步客户端<br>";