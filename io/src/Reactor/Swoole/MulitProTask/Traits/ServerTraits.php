<?php

namespace Bifei\Io\Reactor\Swoole\MulitProTask\Traits;

use Swoole\Event;

trait ServerTraits
{
    // 自定义服务的事件注册函数，
    // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;

    // 连接
    public $socket = null;

    public $clients = [];
    protected $timeIds = [];

    // 需要处理事情
    public function accept()
    {
        Event::add($this->initServer(), $this->createSocket());
        debug(posix_getpid() . "进程设置event 成功");
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
            debug(posix_getpid());
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
            // 如果能接收到消息，那么这个程序一定在心跳检测的范围内
            if (!empty($this->timeIds[(int)$socket])) {
                swoole_timer_clear($this->timeIds[(int)$socket]);
                debug("清空：" . $this->timeIds[(int)$socket]);
            }
            //            debug('该子进程的父级id' . posix_getppid());
            //从连接当中读取客户端的内容
            $buffer = fread($socket, 1024);
            //如果数据为空，或者为false,不是资源类型
            if (empty($buffer)) {
                if (feof($socket) || !is_resource($socket)) {
                    //触发关闭事件
                    swoole_event_del($socket);
                    fclose($socket);
                    debug('断开链接' . (int)$socket);
                    return null;
                }
            }
            //正常读取到数据,触发消息接收事件,响应内容
            if (!empty($buffer) && is_callable($this->onReceive)) {
                ($this->onReceive)($this, $socket, $buffer);
            }
            $this->heartbeatCheck($socket);
        };
    }

    protected function fork()
    {
        for ($i = 0; $i < $this->config['worker_num']; $i++) {
            $son11 = pcntl_fork();
            if ($son11 > 0) {
                // 父进程空间
                pidPut($son11, $this->config['worker_pid_files']);
            } else if ($son11 < 0) {
                // 进程创建失败的时候
            } else {
                // debug(posix_getpid()); // 阻塞
                $this->accept();
                // 处理接收请求
                exit;
            }
        }
        // for ($i=0; $i < $this->config['worker_num']; $i++) {
        //     $status = 0;
        //     $son = pcntl_wait($status);
        //     debug('回收子进程：'.$son);
        // }
    }

    /**
     * 默认不开启
     * 心跳检测
     */
    protected function heartbeatCheck($socket)
    {
        $time = $this->config['heartbeat_check_interval'];
        if (!empty($time)) {
            // 记录客户端上一次的发送时间
            $this->clients[(int)$socket] = time();
            $timeId = swoole_timer_after($time * 1000, function () use ($time, $socket) {
                //判断客户端是否在heartbeat_check_interval这个时间还有信息的动作
                if ((time() - $this->clients[(int)$socket]) >= $time) {
                    swoole_event_del($socket);
                    fclose($socket);
                    debug('断开链接');
                    unset($this->clients[(int)$socket]);
                    debug('结束：' . (int)$socket . "-的链接");
                }
                debug("执行swoole_timer_after");

            });
            // 记录定时器
            $this->timeIds[(int)$socket] = $timeId;
        }
    }
}