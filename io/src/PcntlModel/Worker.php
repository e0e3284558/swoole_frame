<?php

namespace Bifei\Io\PcntlModel;

class Worker
{
    // 自定义服务的事件注册函数
    // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    public $socket;

    // 自定义多个子进程
    protected $config = [
        'worker_num' => 4,

    ];

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
        echo $socket_address;
    }


    // 需要处理事情
    public function accept()
    {
        // 接收连接和处理事情使用
        while (true) {
            var_dump(posix_getpid());
            // 监听的过程是阻塞的
            $client = @stream_socket_accept($this->socket);
            // is_callable判断一个参数是不是闭包
            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this, $client);
            }
            $data = fread($client, 65535);
            if (is_callable($this->onReceive)) {
                ($this->onReceive)($this, $client, $data);
            }
        }
    }

    public function start()
    {
        // 启动服务的
        $this->fork();
    }

    //创建多个子进程，并且让子进程可以运行accept函数
    public function fork()
    {
        for ($i = 0; $i < $this->config['worker_num']; $i++) {
            $son11 = pcntl_fork();
            if ($son11 > 0) {
                // 父进程空间
            } else if ($son11 < 0) {
                echo "失败";
            } else {
                $this->accept();
                // 处理接收请求
                break;
            }
        }
        if ($son11 > 0) {
            $status = 0;
            var_dump(pcntl_wait($status));
        }
    }

    public function set($value)
    {


    }

    public function send($conn, $data)
    {
        $response = "HTTP/1.1 200 OK \r\n";
        $response .= "Content-Type:text/html;charset=UTF-8\r\n";
        $response .= "Connection:keep-alive\r\n";
        $response .= "Content-length:" . strlen($data) . "\r\n\r\n";
        $response .= $data;
        fwrite($conn, $response);
    }


}