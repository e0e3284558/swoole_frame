<?php
namespace App\WebSocket\Controller;

/**
 *
 */
class IndexController
{
    public function open($server, $request)
    {
        dd('indexController open');
    }
    public function message($server, $frame)
    {
        $server->push($frame->fd, "this is server");
    }
    public function close($ser, $fd)
    {

    }
}
