<?php
// 同步客户端
$client = new swoole_client(SWOOLE_SOCK_TCP);

// 连接到服务器
if (!$client->connect('127.0.0.1', 9501, 0.5)) {
    die('连接失败');
}

function order()
{
    // 假设一些操作造成的时间长
    sleep(4);
    return "order \n";
}

// 向服务器发送数据
if (!$client->send(order())) {
    die("发送失败");
}

$data = $client->recv();
if (!$data) {
    die('未接收数据或数据错误');
}

echo $data;

// 关闭连接
$client->close();


echo "订单生成成功\n";