<?php
include 'db.php';
//$serv = new Swoole\Server("127.0.0.1", 9501, SWOOLE_BASE);
$serv = new Swoole\Server("127.0.0.1", 9501);

$serv->set(array(
    'worker_num' => 1,
    // 设置并开启task进程 与 个数
    'task_worker_num' => 4,
));

// 必须的回调函数
$serv->on('Receive', function (Swoole\Server $serv, $fd, $from_id, $data) {
    $db = new Db();
//    $sql = 'select `id`,`name`,`mobile`,`gender` from custimers';
//    $dat = $db->query($sql);
    for ($i = 0; $i < 4; $i++) {
//        $sql = "select `id`,`name`,`mobile`,`gender` from custimers limit {$i}*500000,500000";
//        $dat = $db->query($sql);
        $dat = 1;
        $task_id = $serv->task($dat, $i);
        var_dump($task_id);
    }
    $serv->send($fd, "分发任务，任务id为$task_id\n");
});

// 必须的回调函数
$serv->on('Task', function (Swoole\Server $serv, $task_id, $from_id, $data) {
    var_dump($data);
//    foreach ($data as $key => $value) {
//        $string = $value['name'] . "你好 \n";
//        file_put_contents(__DIR__ . "task.log", "task_id:" . $task_id . "信息 " . $string, 8);
//    }
    $serv->finish($data);
});

$serv->on('Finish', function (Swoole\Server $serv, $task_id, $data) {

});


$serv->start();