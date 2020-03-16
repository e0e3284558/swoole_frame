<?php
// 初始化
$fd = inotify_init();
//  inotify_add_watch|针对某一个文件进行监听   mask|监听的事件  IN_MODIFY
// 多个文件，循环监听
$watch_descriptor = inotify_add_watch($fd, __DIR__ . '/index.php', IN_ATTRIB);
// 读取发生变化的文件
// 是一个阻塞的
//while (true) {
//    $event = inotify_read($fd);
//    var_dump($event);
//}

swoole_event_add($fd, function ($fd) {
    $event = inotify_read($fd);
    var_dump($event);
});