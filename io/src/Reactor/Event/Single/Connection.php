<?php
namespace Bifei\Io\Reactor\Event\Single;

class Connection
{
     protected $conn;
     /**
      * @var \ShineYork\Io\Reactor\Event\Single\Worker
      */
     protected $server;
     protected $readBuffer = '';
     protected $writeBuffer = '';

     public function __construct($conn, Worker $server)
     {
         $this->conn = $conn;
         $this->server = $server;
     }

     public function handler()
     {
         Reactor::getInstance()->add($this->conn, Reactor::READ, $this->read());
     }

     public function read()
     {
        return function($conn){
            $this->server->debug("connection read 读取信息 start");

            $this->readBuffer = '';
            if (is_resource($conn)) {
                while ($content = fread($conn, 65535)) {
                    $this->readBuffer .= $content;
                }
            }
            if ($this->readBuffer) {
                Reactor::getInstance()->add($conn, Reactor::WRITE, $this->write());
            } else {
                Reactor::getInstance()->del($conn);
                fclose($conn);

                $this->server->debug("connection 关闭信息");
            }

            $this->server->debug("connection read 读取信息 send");
            // if (!feof($conn) || !\is_resource($conn)) {
            //     Reactor::getInstance()->del($conn);
            //     fclose($conn);
            // }
        };
     }

     private function write()
     {
         return function($conn){
             $this->server->debug("connection write 发送信息");
             // $this->server->debug('return function');
             if (is_resource($conn)) {
                 if(is_callable($this->server->onMessage)){
                     call_user_func($this->server->onMessage, $this->server, $conn, $this->readBuffer);
                 }
             }
         };
     }
 }
