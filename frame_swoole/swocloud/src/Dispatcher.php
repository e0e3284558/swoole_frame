<?php

namespace SwoCloud;

use Swoole\Server as SwooleServer;
use Swoole\Http\Request;
use Swoole\Http\Response;
use \Redis;

class Dispatcher
{
    public function register(Route $route, SwooleServer $server, $fd, $data)
    {
        dd('register', 'Dispatcher');
        $serverKey = $route->getServerKey();
        // 把服务端信息记录到redis
        $redis = $route->getRedis();
        $value = json_encode([
            'ip' => $data['ip'],
            'port' => $data['port'],
        ]);
        $redis->sadd($serverKey, $value);
        $server->tick(3000, function ($timer_id, Redis $redis, SwooleServer $server, $serverKey, $fd, $value) {
            // 服务器是否正常运行，如果不是就主动清空
            // 并把信息从redis中清除
            if (!$server->exist($fd)) {
                $redis->srem($serverKey, $value);
                $server->clearTimer($timer_id);
                dd('im server 宕机 ，主动清空');
            }
        }, $redis, $server, $serverKey, $fd, $value);
    }

    /**
     * 用户登录的方法
     * @param Route $route
     * @param Request $request
     * @param Response $response
     */
    public function login(Route $route, Request $request, Response $response)
    {
        
    }
}