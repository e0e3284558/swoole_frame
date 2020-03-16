<?php
namespace ShineYork\Io\AsyncModel;

use Swoole\Event;

class WorkerEC
{
    protected $socket = null;

    public function __construct($socketAddress) {
        //监听地址+端口
        $this->socket = stream_socket_server($socketAddress);
    }

    public function start()
    {
        $this->accept();
    }

    public function accept()
    {
        Event::add($this->socket, $this->createSocket());
    }

    public function createSocket()
    {
        return function($socket){
            $client=@stream_socket_accept($socket);
            //触发事件的连接的回调
            if(!empty($client) && is_callable($this->onConnect)){
                call_user_func($this->onConnect,$client);
            }
            // \var_dump($client);
            Event::add($client, $this->sendMessage());
        };
    }
    public function sendMessage()
    {
        return function($socket){
            //从连接当中读取客户端的内容
            $buffer=fread($socket,1024);
            //如果数据为空，或者为false,不是资源类型
            if(empty($buffer)){
                if(feof($socket) || !is_resource($socket)){
                    //触发关闭事件
                    swoole_event_del($socket);
                    fclose($socket);
                }
            }
            //正常读取到数据,触发消息接收事件,响应内容
            if(!empty($buffer) && is_callable($this->onMessage)){
                call_user_func($this->onMessage,$this,$socket,$buffer);
                swoole_event_del($socket);
                fclose($socket);
            }
        };
    }

    public function send($conn, $data)
    {
        fwrite($conn, $data);
    }
}
