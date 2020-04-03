<?php
namespace App\Listener;

use Firebase\JWT\JWT;

use SwoStar\Event\Listener;
use SwoStar\Server\WebSocket\WebSocketServer;
use Swoole\Http\Request;
use Swoole\Http\Response ;
use SwoStar\Console\Input;

class HandShakeListener extends Listener
{
    protected $name = 'ws.hand';

    public function handler(WebSocketServer $server = null, Request  $request = null, Response $response = null)
    {
        // 这是接收websocket连接传递的参数
        $token = $request->header['sec-websocket-protocol'];
        // 进行用户的校验
        if (empty($token) || !($this->check($server, $token, $request->fd))) {
            $response->end();
            return false;
        }
        // websocket的加密过程
        $this->handshake($request, $response);
    }

    protected function check(WebSocketServer $server, $token, $fd)
    {
        try {
            $config = $this->app->make('config');
            $key = $config->get('server.route.jwt.key');
            // 1. 进行jwt验证
            $jwt = JWT::decode($token, $key, $config->get('server.route.jwt.alg'));

            $userInfo = $jwt->data;
            dd($userInfo, '用户信息');
            // 2. 存储信息到redis中
            $url = $userInfo->serverUrl;
            $server->getRedis()->hset($key, $userInfo->uid, \json_encode([
                'fd' => $fd,
                'name' => $userInfo->name,
                'serverUrl' => $url
            ]));

            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    protected function handshake($request, $response)
    {
        // websocket握手连接算法验证
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten          = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
           $response->end();
           return false;
        }
        echo $request->header['sec-websocket-key'];
        $key = base64_encode(sha1(
           $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
           true
        ));

        $headers = [
           'Upgrade'               => 'websocket',
           'Connection'            => 'Upgrade',
           'Sec-WebSocket-Accept'  => $key,
           'Sec-WebSocket-Version' => '13',
        ];

        // WebSocket connection to 'ws://127.0.0.1:9502/'
        // failed: Error during WebSocket handshake:
        // Response must not include 'Sec-WebSocket-Protocol' header if not present in request: websocket
        if (isset($request->header['sec-websocket-protocol'])) {
           $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        foreach ($headers as $key => $val) {
           $response->header($key, $val);
        }

        $response->status(101);
        $response->end();
    }
}
