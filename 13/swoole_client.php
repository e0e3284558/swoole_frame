<?php


// 同步客户端
$client = new swoole_client(SWOOLE_SOCK_TCP|SWOOLE_KEEP);

// 连接到服务器
if (!$client->connect('127.0.0.1', 9501, 0.5)) {
    die('连接失败');
}

$content = '我太帅气了';

//for ($i = 0; $i < 100; $i++) {
    $len = pack('N', strlen($content));
    $send = $len . $content;
    $client->send($send);
//    usleep(10);
//    $client->send("hello {$i} world");
//}
//

$data = $client->recv();
if (!$data) {
    die('未接收数据或数据错误');
}

echo $data;

// 关闭连接
//$client->close();


echo "订单生成成功\n";