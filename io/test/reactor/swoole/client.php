<?php
$client = stream_socket_client("tcp://127.0.0.1:9000");
$old = microtime(true);
// 给socket写信息
fwrite($client, 'hello word');
// 读取信息
var_dump(fread($client, 65535));
$new = microtime(true);
var_dump($new - $old);
// 关闭连接
fclose($client);