<?php

namespace App\Listener;

use SwoStar\Event\Listener;
use SwoStar\Server\WebSocket\WebSocketServer;
use Swoole\Coroutine\Http\Client;

class WSMessageFrontListener extends Listener
{
    protected $name = 'ws.message.front';

    public function handler(WebSocketServer $swoStarServer = null, $swooleServer = null, $frame = null)
    {
        /*
         * 消息的格式需要统一
         * {
         *      'method'=>执行操作
         *      'msg'=》消息
         * }
         */
        $data = json_decode($frame->data, true);
        $this->{$data['method']}($swoStarServer, $swooleServer, $data, $frame->fd);
    }

    /**
     * 服务器广播
     * @param WebSocketServer|null $swoStarServer
     * @param null $swooleServer
     * @param $data
     * @param null $frame
     * @throws \Exception
     */
    protected function serverBroadcast(WebSocketServer $swoStarServer = null, $swooleServer = null, $data, $frame = null)
    {
        $config = $this->app->make('config');
//        dd($config->get('server.route.server.ip'), $config->get('server.route.server.port'));

        $cli = new Client($config->get('server.route.server.host'), $config->get('server.route.server.port'));
        if ($cli->upgrade('/')) {
            $cli->push(json_encode([
                'method' => 'routeBroadcast',
                'msg' => $data['msg'],
            ]));
        }
    }
}