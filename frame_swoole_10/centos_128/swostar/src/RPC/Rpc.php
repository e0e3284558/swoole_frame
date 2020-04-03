<?php
namespace SwoStar\RPC;

use SwoStar\Console\Input;
use Swoole\Server;

class Rpc
{
    protected $host ;

    protected $port;

    public function __construct(Server $server, $config)
    {
        $listen = $server->listen($config['host'], $config['port'], SWOOLE_SOCK_TCP);
        $listen->set($config['swoole']);

        $listen->on('connect', [$this, 'connect']);
        $listen->on('receive', [$this, 'receive']);
        $listen->on('close', [$this, 'close']);

        Input::info('tcp监听的地址: '.$config['host'].':'.$config['port'] );
    }

    public function connect($serv, $fd){
        dd("超管查房");
    }

    public function receive($serv, $fd, $from_id, $data) {
        $serv->send($fd, 'Swoole: '.$data);
        $serv->close($fd);
    }

    public function close($serv, $fd) {
        echo "Client: Close.\n";
    }
}
