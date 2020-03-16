<?php

$http = new Http('0.0.0.0', 9501);
$http->start();


class Http
{
    protected $http;
    protected $config=[
        'worker_num'=>1
    ];

    function __construct($ip, $port)
    {
        $this->http = new Swoole\Http\Server($ip, $port);
        $this->http->set($this->config);
        echo $ip . ":" . $port . "\n";
        $this->http->on('request', [$this, 'request']);
    }

    public function request($request, $response)
    {
        var_dump(0);
        var_dump($request->get, $request->post);
        $response->header("Content-Type", "text/html;charset=utf-8");
        $response->end("<h1>Hello Swoole.#" . rand(1000, 9999) . "</h1>");
    }

    public function start()
    {
        $this->http->start();
    }
}
