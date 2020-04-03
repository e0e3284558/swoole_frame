<?php
namespace App\Listener;

use SwoStar\Server\WebSocket\Connections;
use SwoStar\Server\WebSocket\WebSocketServer;
use SwoStar\Event\Listener;
use Firebase\JWT\JWT;

class WSCloseListener extends Listener
{
    protected $name = 'ws.close';

    public function handler(WebSocketServer $swoStarServer = null, $swooleServer  = null, $fd = null)
    {
        // 获取删除的用户 -> jwt -> token  -> header -> request
        $request = Connections::get($fd)['request'];
        $token = $request->header['sec-websocket-protocol'];

        $config = $this->app->make('config');
        $key = $config->get('server.route.jwt.key');
        // 1. 进行jwt验证
        $jwt = JWT::decode($token, $key, $config->get('server.route.jwt.alg'));
        // 删除
        $swoStarServer->getRedis()->hDel($key, $jwt->data->uid);
    }


}
