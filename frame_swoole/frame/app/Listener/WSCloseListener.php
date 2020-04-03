<?php

namespace App\Listener;

use Firebase\JWT\JWT;
use SwoStar\Event\Listener;
use SwoStar\Server\WebSocket\Connections;
use SwoStar\Server\WebSocket\WebSocketServer;

class WSCloseListener extends Listener
{
    protected $name = 'ws.close';

    public function handler(WebSocketServer $swoStarServer = null, $swooleServer = null, $fd = null)
    {
        // jwt去获取连接的用户id jwt->token->header->request
        $request = Connections::get($fd)['request'];
        $token = $request->header['sec-websocket-protocol'];

        $config = $this->app->make('config');
        $key = $config->get('server.route.jwt.key');
        $jwt = JWT::decode($token, $key, $config->get('server.route.jwt.alg'));

        //删除
        $swoStarServer->getRedis()->hDel($key, $jwt->data->uid);
    }
}