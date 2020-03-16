<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Bifei\Io\Reactor\Swoole\Mulit\Worker;

$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);

$server->stop(); // 暂时只是停止子进程