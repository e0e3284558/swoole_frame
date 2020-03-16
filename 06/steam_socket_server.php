<?php
$host = 'tcp://0.0.0.0:9000';
$server = stream_socket_server($host);


echo $host . "\n";

// 建立与客户端的连接
// 服务处于一个挂起的状态->等待连接进来并且创建连接
// stream_socket_accept是阻塞
// 监听连接->可以重复监听
while (true) {
    $client = @stream_socket_accept($server);
    sleep(3);
    var_dump(fread($client, 65535));
    fwrite($client, 'server hello world');
    fclose($client);
    var_dump($client);
}


