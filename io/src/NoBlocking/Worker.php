<?php

namespace Bifei\Io\NoBlocking;

class Worker
{
    // 自定义服务的事件注册函数
    // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;
    public $socket;

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
        // 设置套接字为非阻塞
//        stream_set_blocking($this->socket, 0);
        echo $socket_address;
    }

    // 需要处理事情
    public function accept()
    {
        // 接收连接和处理事情使用
        while (true) {
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
//            fwrite($client, 'server hello world');
            // 处理完成后关闭连接
            fclose($client);
            // 心跳检测
        }
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

    public function start()
    {
        // 启动服务的
        $this->accept();
    }
}