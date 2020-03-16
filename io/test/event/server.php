<?php
require __DIR__ . '/../../vendor/autoload.php';

use Bifei\Io\AsyncModel\Worker;

$host = "tcp://0.0.0.0:9000";
$server = new Worker($host);
$server->onReceive = function ($socket, $client, $data) {
    send($client, "hello world client \n");
};
debug($host);
$server->start();