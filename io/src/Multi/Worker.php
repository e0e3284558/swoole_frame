<?php

namespace Bifei\Io\Multi;

class Worker
{
    // 自定义服务的事件注册函数
    // 这三个是闭包函数
    public $onReceive = null;
    public $onConnect = null;
    public $onClose = null;

    public $sockets = [];

    public $socket;

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
        // 设置非阻塞
        stream_set_blocking($this->socket, 0);
        $this->sockets[(int)$this->socket] = $this->socket;
        echo $socket_address;
    }

    // 需要处理事情
    public function accept()
    {
        // 接收连接和处理事情使用
        while (true) {
            $read = $this->sockets;

//            $this->debug('这是stream_select检测start的$read');
//            $this->debug($read, true);
            // 效验池子是否有可用的连接
            // 把链接放到$read
            stream_select($read, $w, $e, 1);

//            $this->debug('这是stream_select检测end的$read');
//            $this->debug($read, true);
//            sleep(1);

            foreach ($read as $socket) {
                // $socket 可能为主worker
                // 也可能是通过 stream_socket_accept 创建的连接
                if ($socket === $this->socket) {
                    // 创建与客户端的连接
                    $this->createSocket();
                } else {
                    // 发送信息
                    $this->sendMessage($socket);
                }
            }
//            // 监听的过程是阻塞的
//            $client = @stream_socket_accept($this->socket);
//            // is_callable判断一个参数是不是闭包
//            if (is_callable($this->onConnect)) {
//                // 执行函数
//                ($this->onConnect)($this, $client);
//            }
//
//            $data = fread($client, 65535);
//            if (is_callable($this->onReceive)) {
//                ($this->onReceive)($this, $client, $data);
//            }
////            fwrite($client, 'server hello world');
//            // 处理完成后关闭连接
////            fclose($client);
//            // 心跳检测
        }
    }


    public function createSocket()
    {
        $client = @stream_socket_accept($this->socket);
        // is_callable判断一个参数是不是闭包
        if (is_callable($this->onConnect)) {
            // 执行函数
            ($this->onConnect)($this, $client);
        }
        // 把创建的socket的链接->放到sockets[]
        $this->sockets[(int)$client] = $client;
        return $client;

    }

    public function sendMessage($client)
    {
        $data = fread($client, 65535);
        if ($data === '' || $data == false) {
//            fclose($client);
//            unset($this->sockets[(int)$client]);
            return null;
        }
        if (is_callable($this->onReceive)) {
            ($this->onReceive)($this, $client, $data);
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

    public function debug($data, $flag = false)
    {
        if ($flag) {
            var_dump($data);
        } else {
            echo "===>>>:" . $data . "\n";
        }
    }

    public function start()
    {
        // 启动服务的
        $this->accept();
    }
}