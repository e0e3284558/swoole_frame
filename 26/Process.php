<?php
require_once "Input.php";
for ($i = 0; $i < 2; $i++) {
    $process = new Swoole\Process(function ($processSon) {
        //子进程空间
        Input::info($processSon->pop(), 'son');
        $processSon->push('hello 子进程 我是father');
    });
    $process->useQueue(1, 2);// 启用消息队列通讯

    $process->push('hello 子进程 我是father');

    $pid = $process->start();
}
