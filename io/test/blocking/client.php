<?php
$client = stream_socket_client("tcp://127.0.0.1:9000");
$new = time();
// 给socket写信息
while (true) {
    fwrite($client, 'hello word');
    // 读取信息
    var_dump(fread($client, 65535));
    sleep(2);
}

// 关闭连接
fclose($client);