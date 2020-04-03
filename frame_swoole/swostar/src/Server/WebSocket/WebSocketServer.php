<?php

namespace SwoStar\Server\WebSocket;

use SwoStar\Console\Input;
use SwoStar\Server\Http\HttpServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
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
        $event = [
            'request' => 'onRequest',
            'open' => 'onOpen',
            'message' => 'onMessage',
            'close' => 'onClose'
        ];
        // 判断是否自定义握手的过程
        ( ! $this->app->make('config')->get('server.ws.is_handshake'))?: $event['handshake'] = 'onHandShake';

        $this->setEvent('sub', $event);
    }

    public function onHandShake(SwooleRequest $request, SwooleResponse $response)
    {
        $this->app->make('event')->trigger('ws.hand',[$this,$request,$response]);
        // 因为设置了onHandShake回调函数，就不会触发onOpen
        $this->onOpen($this->swooleServer,$request);
    }

//    protected function initSetting()
//    {
//        $config = app('config');
//        $this->port = $config->get('server.ws.port');
//        $this->host = $config->get('server.ws.host');
//        $this->config = $config->get('server.ws.swoole');
//    }
 
    public function onOpen(SwooleServer $server, $request)
    {
        // 需要获取用户访问的地址
        Connections::init($request->fd, $request);
        echo "服务端：和--{$request->fd}--握手成功 \n";
        // 获取访问的地址
        $return = app('route')->setFlag('WebSocket')->setMethod('open')->match($request->server['path_info'], [$server, $request]);
    }

    public function onMessage(SwooleServer $server, $frame)
    {
        $path = (Connections::get($frame->fd))['path'];
        // 消息回复的事件
        $this->app->make('event')->trigger('ws.message.front',[$this,$server,$frame]);

        // 消息的业务流程
        echo "接收到:{$frame->fd}:{$frame->data},操作码:{$frame->opcode},完成:{$frame->finish}\n";
        $return = app('route')->setFlag('WebSocket')->setMethod('message')->match($path, [$server, $frame]);
    }

    public function onClose($ser, $fd)
    {
        echo "客户端 {$fd} 关闭 \n";

        $path = (Connections::get($fd))['path'];
        $return = app('route')->setFlag('WebSocket')->setMethod('close')->match($path, [$ser, $fd]);
        $this->app->make('event')->trigger('ws.close',[$this,$ser,$fd]);
        Connections::del($fd);
    }
}