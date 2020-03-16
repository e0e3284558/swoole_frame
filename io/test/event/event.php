<?php

use \Event as Event;
use \EventBase as EventBase;

$eventBase = new EventBase();
$event = new Event($eventBase, -1, Event::PERSIST | Event::TIMEOUT, function () {
    echo "hello world event \n";
});
$event1 = new Event($eventBase, -1, Event::PERSIST | Event::TIMEOUT, function () {
    echo "hello world event -0.2 \n";
});
$event1->add();
$event->add();
$eventBase->loop();

// EventBase=>  事件库 => 存储创建的事件
// event 是一个事件
// event =》 add 添加一个事件
// $eventBase ->loop() 循环执行事件
// Event::PERSIST 表示事件的循环执行 ->针对闭包函数，不是针对event对象类
// Event::TIMEOUT 表示间隔多久执行 ->针对闭包函数，不是针对event对象类
// -1：计时器
// 信号：信号标识  SIGIO，SIGHUP