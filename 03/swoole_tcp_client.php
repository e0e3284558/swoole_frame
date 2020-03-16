<?php
$client = new swoole_client(SWOOLE_SOCK_TCP);

//连接到服务器
if (!$client->connect('106.13.78.8', 9501, 0.5)) {
    die("connect failed.");
}

for ($i = 0; $i < 100; $i++) {
    usleep(10);
    $client->send("hello world\n");
}


//向服务器发送数据
//if (!$client->send("hello world"))
//{
//    die("send failed.");
//}


//从服务器接收数据
$data = $client->recv();
if (!$data) {
    die("recv failed.");
}
echo $data;
//关闭连接
$client->close();

echo "<br>同步客户端<br>";