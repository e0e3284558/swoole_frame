<?php
$client = stream_socket_client("tcp://127.0.0.1:9000");
$old = microtime(true);
// 给socket写信息
fwrite($client, '第一次信息');
echo "第一次信息\n";
// 读取信息
var_dump(fread($client, 65535));

//sleep(1);
fwrite($client, '第二次信息');
echo "第二次信息\n";
// 读取信息
var_dump(fread($client, 65535));

//sleep(4);
fwrite($client, '第三次信息');
echo "第三次信息\n";

// 读取信息
var_dump(fread($client, 65535));




$new = microtime(true);

var_dump($new - $old);
// 关闭连接
//fclose($client);