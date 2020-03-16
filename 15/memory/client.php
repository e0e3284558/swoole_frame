<?php
$client = new swoole_client(SWOOLE_SOCK_TCP);

//连接到服务器
$client->connect('127.0.0.1', 9800, 0.5);
//向服务器发送数据
$body = 'a';
$client->send($body);
//从服务器接收数据
$data = $client->recv();
//关闭连接
$client->close();

echo "其他事情\n";
 
