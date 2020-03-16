<?php
$key = ftok(__FILE__, 'u');
echo $key . "\n";

$queue = msg_get_queue($key);

msg_receive($queue,10,$msgtype,1024,$message);
var_dump($msgtype);
echo "接收到消息\n";
var_dump($message);