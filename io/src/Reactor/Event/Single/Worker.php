<?php
namespace Bifei\Io\Reactor\Event\Single;

// io阻塞模型
class Worker{
    //监听socket
    public $socket = NULL;
    //连接事件回调
    public $onConnect = NULL;
    //接收消息事件回调
    public $onMessage = NULL;
    protected $reactor;

    public function __construct($socketAddress) {
        //监听地址+端口
        $this->socket = stream_socket_server($socketAddress);
        stream_set_blocking($this->socket, 0);
    }

    public function start()
    {
        Reactor::getInstance()->add($this->socket, Reactor::READ, $this->createSocket());
        Reactor::getInstance()->run();
    }

    public function createSocket()
    {
        return function($socket){
            $client = stream_socket_accept($socket);
            stream_set_blocking($client, false);
            if(is_callable($this->onConnect)){//socket连接成功并且是我们的回调
                //触发事件的连接的回调
                call_user_func($this->onConnect, $client);
            }

            $this->debug("worker createSocket 设置处理执行请求 handler ");
            (new Connection($client, $this))->handler();
        };
    }
    public function debug($data, $flag = false)
    {
        if ($flag) {
            \var_dump($data);
        } else {
            echo "==== >>>> : ".$data." \n";
        }
    }
    //响应http请求
    public function send($conn,$content){
        $http_resonse = "HTTP/1.1 200 OK\r\n";
        $http_resonse .= "Content-Type: text/html;charset=UTF-8\r\n";
        $http_resonse .= "Connection: keep-alive\r\n";
        $http_resonse .= "Server: php socket server\r\n";
        $http_resonse .= "Content-length: ".strlen($content)."\r\n\r\n";
        $http_resonse .= $content;
        fwrite($conn, $http_resonse);
    }
}
