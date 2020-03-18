<?php

namespace App\WebSocket\Controller;

class IndexController
{
    public function open($server, $request)
    {
        dd('IndexController open');
    }

    public function message($server, $request)
    {

    }

    public function close($ser, $fd)
    {

    }
}