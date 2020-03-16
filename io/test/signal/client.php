<?php
// 安装信号
pcntl_signal(SIGIO, "sig_handler");
function sig_handler($sig)
{
    sleep(2);
    echo "这是测试信号的一个测试类";
}

// 是一个安装信号的操作
// pid => 进程id 要设置的信号
// 根据进程设置信号
// posix_getpid获取进程id的
posix_kill(posix_getpid(), SIGIO);
// 分发
echo "其他事情 \n";
pcntl_signal_dispatch();

// 信号是配置多进程使用
// posix_getpid => 只会针对于与当前的进程去设置信号