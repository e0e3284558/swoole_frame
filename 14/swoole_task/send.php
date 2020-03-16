<?php
$server = new Swoole\Server("0.0.0.0", 9501);
$server->set([
    'worker_num' => 2,
    'task_worker_num' => 2,
]);
$server->on('pipeMessage', function ($server, $src_worker_id, $data) {
    echo "#{$server->worker_id} message from #$src_worker_id \n";
});
$server->on('task', function ($server, $task_id, $reactor_id, $data) {
    $server->sendMessage("\n hello task process :", 1);
});
$server->on('finish', function ($server, $fd, $reactor_id) {

});
$server->on('receive', function (swoole_server $server, $fd, $reactor_id, $data) {
    $server->task(7);
});

$server->start();