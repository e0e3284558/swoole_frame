<?php
$client = stream_socket_client("tcp://127.0.0.1:9000");
// 给socket写信息
fwrite($client,'helloword');
// 读取信息
var_dump(fread($client,65535));
// 关闭连接
fclose($client);