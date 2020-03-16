<?php
$server = new Swoole\Server("0.0.0.0", 9000);

$key = ftok(__DIR__, 1);
echo $key;
$server->set([
    'worker_num' => 2,
    'task_worker_num' => 2,
    'task_ipc_mode' => 2,
    'message_queue_key' => $key,
]);

// 消息发送过来
$server->on('receive', function (swoole_server $server, int $fd, int $reactor_id, string $data) {
    $server->task(7);
    $server->send($fd, 1);
});

// ontask事件回调
$server->on('task', function (swoole_server $server, $task_id, $form_id, $data) {
    echo "接收到消息\n";
    var_dump($server->worker_id);
//    echo strlen($data) . "\n";
//    try {
//
//    } catch (Exception $e) {
//        $server->sendMessage("task数据",1);
//
//    }
    $server->sendMessage("task数据", 0);

    $server->finish("执行完毕");
});

$server->on('finish', function ($server, $task_id, $data) {

});
$server->on('PipeMessage', function (swoole_server $server, $src_worker_id, $message) {
    echo "\n 接收到数据 \n";
    var_dump($message);
});

$server->start();