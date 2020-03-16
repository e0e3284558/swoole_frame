<?php

namespace Bifei\Io\Reactor\Swoole\Mulit;

use Swoole\Event;

class Worker
{
    // 自定义服务的事件注册函数，
    // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;

    // 连接
    public $socket = null;
    // 创建多个子进程 -》 是不是可以自定义
    protected $config = [
        'worker_num' => 4,
        'worker_pid_files' => __DIR__ . '/workerPids.txt',
        'context' => [
            'socket' => [
                // 设置等待资源的个数
                'backlog' => '102400'
            ],
        ],
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

    // 需要处理事情
    public function accept()
    {
        Event::add($this->initServer(), $this->createSocket());
    }


    public function initServer()
    {
        // 并不会起到太大的影响
        // 这里是参考与workerman中的写法
        $context = stream_context_create($this->config['context']);
        // 设置端口可以重复监听
        \stream_context_set_option($context, 'socket', 'so_reuseport', 1);

        // 传递一个资源的文本 context
        return $this->socket = stream_socket_server($this->socket_address, $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);
    }

    public function createSocket()
    {
        return function ($socket) {
            // debug(posix_getpid());
            // $client 是不是资源 socket
            $client = stream_socket_accept($this->socket);
            // is_callable判断一个参数是不是闭包
            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this, $client);
            }
            // 默认就是循环操作
            Event::add($client, $this->sendClient());
        };
    }

    public function sendClient()
    {
        return function ($socket) {
            //从连接当中读取客户端的内容
            $buffer = fread($socket, 1024);
            //如果数据为空，或者为false,不是资源类型
            if (empty($buffer)) {
                if (feof($socket) || !is_resource($socket)) {
                    //触发关闭事件
                    swoole_event_del($socket);
                    fclose($socket);
                }
            }
            //正常读取到数据,触发消息接收事件,响应内容
            if (!empty($buffer) && is_callable($this->onReceive)) {
                ($this->onReceive)($this, $socket, $buffer);
            }
        };
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
        $this->stop();
        $this->fork();

        // $workerPids = $this->workerPids;
        // debug("reloadSig start");
        // debug($workerPids, true);
        // foreach ($workerPids  as $key => $workerPid) {
        //     posix_kill($workerPid, 9);
        //     debug("结束：".$workerPid);
        //     unset($this->workerPids[$key]);
        //     // 删掉一个进程，重启一个进程
        //     $this->fork(1);
        // }
        // debug("reloadSig end");
        // debug($this->workerPids, true);
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

    public function stop()
    {
        $workerPids = pidGet($this->config['worker_pid_files']);
        foreach ($workerPids as $key => $workerPid) {
            posix_kill($workerPid, 9);
        }
    }

    // 启动服务的
    public function start()
    {
        debug('start 开始 访问：' . $this->socket_address);
        pidPut(null, $this->config['worker_pid_files']);
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

    public function fork($workerNum = null)
    {
        $workerNum = (empty($workerNum)) ? $this->config['worker_num'] : $workerNum;
        for ($i = 0; $i < $workerNum; $i++) {
            $son11 = pcntl_fork();
            if ($son11 > 0) {
                // 父进程空间
                pidPut($son11, $this->config['worker_pid_files']);
                $this->workerPids[] = $son11;
            } else if ($son11 < 0) {
                // 进程创建失败的时候
            } else {
                // debug(posix_getpid()); // 阻塞
                $this->accept();
                // 处理接收请求
                break;
            }
        }
        // for ($i=0; $i < $this->config['worker_num']; $i++) {
        //     $status = 0;
        //     $son = pcntl_wait($status);
        //     debug('回收子进程：'.$son);
        // }
    }
}

