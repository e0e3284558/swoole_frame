<?php

namespace App\Listener;

use SwoStar\Event\Listener;
use SwoStar\Server\Server;

class StartListener extends Listener
{
    protected $name = 'start';

    public function handler($swoStarServer = null, $swooleServer  = null)
    {
        dd('this is StartListener handler', 'StartListener');
        $config = $this->app->make('config');
        go(function () use ($swoStarServer,$config) {
            $cli = new \Swoole\Coroutine\Http\Client($config->get('server.route.server.host'), $config->get('server.route.server.port'));
            // 升级为webSocket
            if ($cli->upgrade("/")) {
                // 这是本机信息
                $data=[
                    'method'     =>'register', //方法
                    'serviceName'=>'IM1',
                    'ip'         => '106.13.78.8',
                    'port'       => $swoStarServer->getPort()
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