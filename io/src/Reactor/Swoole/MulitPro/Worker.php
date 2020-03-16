<?php

namespace Bifei\Io\Reactor\Swoole\MulitPro;

use Bifei\Io\Reactor\Swoole\MulitPro\Traits\InnotifyTraits;
use Bifei\Io\Reactor\Swoole\MulitPro\Traits\ServerTraits;
use Swoole\Event;

class Worker
{
    // 这是woker server的超类
    use ServerTraits;
    // 文件监听热加载
    use InnotifyTraits;
    // 创建多个子进程 -》 是不是可以自定义
    protected $config = [
        'worker_num' => 4,
        'worker_pid_files' => __DIR__ . '/pid/workerPids.txt',
        'master_pid_files' => __DIR__ . '/pid/masterPids.txt',
        'context' => [
            'socket' => [
                // 设置等待资源的个数
                'backlog' => '102400'
            ],
        ],
        'watch_file'=>false,
        // 单位是秒
        'heartbeat_check_interval'=>3,

    ];

    protected $socket_address = null;
    // 记录子进程pid地址
    protected $workerPidFiles = __DIR__ . "/workerPids.txt";
    // 以内存的方式存pids
    protected $workerPids = [];

    public function __construct($socket_address)
    {
        $this->socket_address = $socket_address;
    }



    public function reloadSig()
    {
        // 先停止运行的进程
        $this->stop();
        pidPut(null, $this->config['worker_pid_files']);
        // 清空记录
        $this->fork();
    }

    public function reload()
    {
        $this->stop(false);
        $this->fork();

    }


    public function sigHandler($sig)
    {
        switch ($sig) {
            case SIGUSR1:
                //重启
                $this->reloadSig();
                break;
            case SIGKILL:
                // 停止
                $this->stop();
                break;
        }
    }

    public function stop($masterKill = true)
    {
        // 杀死子进程
        $workerPids = pidGet($this->config['worker_pid_files']);
        foreach ($workerPids as $key => $workerPid) {
            posix_kill($workerPid, 9);
        }
        // 杀死父进程
        if ($masterKill){
            $masterPid = (pidGet($this->config['master_pid_files']))[0];
            posix_kill($masterPid, 9);
        }

    }

    // 启动服务的
    public function start()
    {
        debug('start 开始 访问：' . $this->socket_address);
        pidPut(null, $this->config['worker_pid_files']);
        pidPut(null, $this->config['master_pid_files']);
        // 记录的是父进程id
        pidPut(posix_getpid(), $this->config['master_pid_files']);
        debug('当前主进程的pid：'.posix_getpid());
        $this->fork();

        // 答案是后
        $this->monitorWorkersForLinux();
    }

    public function monitorWorkersForLinux()
    {
        // 信号安装
        pcntl_signal(SIGUSR1, [$this, 'sigHandler'], false);
        while (1) {
            // Calls signal handlers for pending signals.
            \pcntl_signal_dispatch();
            // Suspends execution of the current process until a child has exited, or until a signal is delivered
            \pcntl_wait($status);
            // Calls signal handlers for pending signals again.
            \pcntl_signal_dispatch();
        }
    }


}

