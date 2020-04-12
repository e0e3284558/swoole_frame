<?php
require_once "Input.php";
for ($i = 0; $i < 3; $i++) {
    $process = new Swoole\Process(function ($processSon) {
        //子进程空间
        Input::info($processSon->read(), '读取到父进程空间的信息');
//        $processSon->write('接收到信息:');
    }, false, true);
    $pid = $process->start();
    $process->write('hello 子进程:' . $pid);

    $workerPool[$pid] = $process;

    swoole_event_add($process->pipe, function ($pipe) use ($process) {
        Input::info($process->read(), '读取到子进程空间的信息');
    });

}
