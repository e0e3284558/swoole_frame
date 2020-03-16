<?php
$client = stream_socket_client("tcp://127.0.0.1:9000");

var_dump($client);
echo (int) $client;
exit();

// 设置非阻塞的状态
stream_set_blocking($client, 0);

$new = time();
// 给socket写信息
//while (true) {
// 创建订单
fwrite($client, 'hello word');
// 读取信息
var_dump(fread($client, 65535));
//    sleep(2);
//}
// 响应
echo "其他的业务\n";
echo time()->$new . "\n";

$r = 0;
// swoole_timer_tick() //异步获取
$read = $write = $except = null;
// 检测的方式根据数组->进行检测socket状态
$read[] = $client;
while (!feof($client)) {
    $read[] = $client;
    // 接受的数据包大小
    var_dump(fread($client, 65535));
    echo $r++ . "\n";
    sleep(1);
    echo "检测socket\n";
    // 返回结果 0 可用  1正忙
    var_dump(stream_select($read, $write, $except, 0));
}