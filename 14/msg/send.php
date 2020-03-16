<?php
$key = ftok(__FILE__, 'u');
echo $key . "\n";

$queue = msg_get_queue('1963012383');
echo "发送消息至队列";
msg_send($queue, 10, 3);