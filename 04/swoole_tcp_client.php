<?php
$old = microtime(true);
$client = new swoole_client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);

//连接到服务器
if (!$client->connect('106.13.78.8', 9501, 0.5)) {
    die("connect failed.");
}
$content = '我太帅气了';
$len = pack('N', strlen($content));
$send = $len . $content;
for ($i = 0; $i < 100; $i++) {
    $client->send($send);
//    usleep(10);
//    $client->send("hello {$i} world");
}
//



//$client->send(str_repeat('djv--', 1024 * 1024 * 1));

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
//$client->close();
$new = microtime(true);
echo "<br>同步客户端<br>";
echo $new - $old;