<?php
class swoole
{
    CONST HOST = "0.0.0.0";
    CONST PORT = 9800;
    public $server = null;
    public $info = [];

    public function __construct()
    {
        $this->server = new Swoole\Server(self::HOST, self::PORT);   //创建server对象
        echo self::HOST.":".self::PORT."\n";
        $this->server->on('receive', [$this, 'onReceive']);
        $this->server->start();
    }

    public function onReceive($server, $fd, $reactor_id, $data)
    {
        $this->info[count($this->info)] = time();
        var_dump(count($this->info));
        $server->send($fd, 1);
    }
}
new swoole();
