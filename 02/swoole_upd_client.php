<?php
$client = new swoole_client(SWOOLE_SOCK_UDP);
$client->sendTo('127.0.0.1', 9001, 'upd');

// 接收服务器信息
$data = $client->recv();

echo "oo";