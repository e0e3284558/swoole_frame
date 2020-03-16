<?php
//$serv = new Swoole\Server("127.0.0.1", 9501, SWOOLE_BASE);
$serv = new Swoole\Server("127.0.0.1", 9501);

$key = ftok(__FILE__, 'u');
echo $key . "\n";

$serv->set(array(
    'worker_num' => 2,
    // 设置并开启task进程 与 个数
    'task_worker_num' => 4,
    'task_ipc_mode' => 2,
    'message_queue_key' => $key,
    'open_length_check' => true,

    //粘包处理
    'package_max_length' => 1024 * 1024 * 3,
    // 类型
    'package_length_type' => 'N',
    // 数据从0开始
    'package_length_offset' => 0,
    // 4 是因为我们选择的pack的类型N是4位
    'package_body_offset' => 4,
));

// 必须的回调函数
$serv->on('Receive', function (Swoole\Server $serv, $fd, $from_id, $data) {
    // worker 投递给task任务的函数
    $t = time();
//    var_dump($t);
    $r = str_repeat('a', 10 * 1024);
    $task_id = $serv->task($r);
    echo "测试阻塞 \n";
    $serv->send($fd, "分发任务，任务id为$task_id\n");
});

// 必须的回调函数
$serv->on('Task', function (Swoole\Server $serv, $task_id, $from_id, $data) {
    // 执行worker所投递的任务
    var_dump(posix_getpid());

    sleep(3);
    var_dump('worker信息:->' . strlen($data));
    // 通知worker
    $serv->finish($data);
});

$serv->on('Finish', function (Swoole\Server $serv, $task_id, $data) {
//    echo "Task#$task_id finished, data_len=" . strlen($data) . PHP_EOL;
});

//$serv->on('workerStart', function ($serv, $worker_id) {
//    global $argv;
//    if ($worker_id >= $serv->setting['worker_num']) {
//        swoole_set_process_name("php {$argv[0]}: task_worker");
//    } else {
//        swoole_set_process_name("php {$argv[0]}: worker");
//    }
//});

$serv->start();