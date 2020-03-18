<?php

namespace SwoStar\Server\WebSocket;

use SwoStar\Console\Input;
use SwoStar\Server\Http\HttpServer;
use Swoole\WebSocket\Server as SwooleServer;

class WebSocketServer extends HttpServer
{

    public function createServer()
    {
        $this->swooleServer = new SwooleServer($this->host, $this->port);
        Input::info('webSocket server访问:ws://106.13.78.8:' . $this->port);
    }

    protected function initEvent()
    {
        $this->setEvent('sub', [
            'request' => 'onRequest',
            'open'=>'onOpen',
            'message'=>'onMessage',
            'close'=>'onClose'
        ]);
    }


    public function onOpen(SwooleServer $server, $request)
    {
        echo "服务端：和{$request->fd}握手成功";
    }

    public function onMessage(SwooleServer $server, $frame)
    {
        echo "接收到:{$frame->fd}:{$frame->data},操作码:{$frame->opcode},完成:{$frame->finish}\n";
        $server->push($frame->fd, '这是服务端');
    }

    public function onClose($ser, $fd)
    {
        echo "客户端 {$fd} 关闭";
    }
}