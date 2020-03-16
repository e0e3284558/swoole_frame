<?php
require 'e.php';

use \Event as Event;
use \EventBase as EventBase;

$socket_address = "tcp://0.0.0.0:9000";
echo $socket_address . "\n";
$server = stream_socket_server($socket_address);


$eventBase = new EventBase();
$count = [];


$event = new Event($eventBase, $server, Event::PERSIST | Event::READ |
    Event::WRITE, function ($socket) use ($eventBase,&$count) {
    // 在闭包中的function($socket)的$socket是构造函数$server传递的值
    // $socket=$server
    // 建立与用户的连接
    echo "连接 start \n";
    $client = stream_socket_accept($socket);
    (new E($eventBase, $client,$count))->handler();
    echo "连接 end \n";

});

$event->add();
$count[(int)$server][Event::PERSIST | Event::READ | Event::WRITE] = $event;
$eventBase->loop();