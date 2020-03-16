<?php
$key = ftok(__FILE__, 'u');
echo $key . "\n";

$queue = msg_get_queue($key);
var_dump($queue);


$r = 0;


$son = pcntl_fork();
if ($son == 0) {
    // 子进程空间
    msg_receive($queue, 10, $msgtype,1024,$message);
    var_dump("父进程的message:".$message);
} else {
    sleep(3);
    echo "向子进程发送信息\n";
    // 父进程空间
    $r = 3;
    msg_send($queue, 10, $r);


}