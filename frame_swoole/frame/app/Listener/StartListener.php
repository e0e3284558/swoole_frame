<?php

namespace App\Listener;

use SwoStar\Event\Listener;
use SwoStar\Server\Server;

class StartListener extends Listener
{
    protected $name = 'start';

    public function handler(Server $server = null)
    {
        dd('this is StartListener handler', 'StartListener');

        go(function () use ($server) {
            $cli = new \Swoole\Coroutine\Http\Client('106.13.78.8', 9500);
            // 升级为webSocket
            if ($cli->upgrade("/")) {
                // 这是本机信息
                $data = [
                    'ip' => $server->getHost(),
                    'port' => $server->getPort(),
                    'ServerName' => 'swostart_im1',
                    'method' => 'register',
                ];
                $cli->push(json_encode($data));
                // 定时器保证长连接
                // 使用通过触发定时判断，不用heartbeat_check_interval 的方式检测
                // 需要主动清空redis定时器
                swoole_timer_tick(3000, function () use ($cli) {
                    $cli->push('', WEBSOCKET_OPCODE_PING);
                });
//                $cli->close();
            }
        });
    }
}