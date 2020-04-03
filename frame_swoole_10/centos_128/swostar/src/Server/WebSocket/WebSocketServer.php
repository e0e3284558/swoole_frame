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

        Input::info('WebSocket server 访问 : ws://192.168.186.130:'.$this->port );
    }

    protected function initEvent(){
        $this->setEvent('sub', [
            'request' => 'onRequest',
               'open' => "onOpen",
            'message' => "onMessage",
              'close' => "onClose",
        ]);
    }

    public function onOpen(SwooleServer $server, $request) {
        // 需要获取访问的地址？
        Connections::init($request->fd, $request->server['path_info']);

        $return = app('route')->setFlag('WebSocket')->setMethod('open')->match($request->server['path_info'], [$server, $request]);
    }

    public function onMessage(SwooleServer $server, $frame) {
        $path = (Connections::get($frame->fd))['path'];

        $return = app('route')->setFlag('WebSocket')->setMethod('message')->match($path, [$server, $frame]);
    }

    public function onClose($ser, $fd) {
        $path = (Connections::get($fd))['path'];

        $return = app('route')->setFlag('WebSocket')->setMethod('close')->match($path, [$server, $fd]);

        Connections::del($fd);
    }

}
