<?php
namespace App\Listener;

use SwoStar\Event\Listener;

use SwoStar\Server\Server;

class StartListener extends Listener
{
    protected $name = 'start';

    public function handler()
    {
        dd('this is StartListener handler', 'StartListener');

        go(function(){
            $cli = new \Swoole\Coroutine\Http\Client('192.168.186.130', 9500);
            if ($cli->upgrade("/")) {
                // 这是本机信息
                $data = [
                    'ip'         => '192.168.186.128',
                    'port'       => 9000,
                    'serverName' => 'swostar_im1',
                    'method'     => 'register',
                ];
                $cli->push(\json_encode($data));
                \swoole_timer_tick(3000, function() use ($cli){
                    $cli->push('', WEBSOCKET_OPCODE_PING);
                });

                // $cli->close();
            }
        });
    }
}
