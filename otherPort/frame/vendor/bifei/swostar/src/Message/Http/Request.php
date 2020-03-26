<?php

namespace SwoStar\Message\Http;

use Swoole\Http\Request as SwooleRequest;
class Request
{
    protected $method;
    protected $uriPath;
    protected $swooleRequest;

    public function getMethod()
    {
        return $this->method;
    }

    public function getUriPath()
    {
        return $this->uriPath;
    }

    /**
     * 初始化
     * @param SwooleRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    public static function init(SwooleRequest $request)
    {
        $self = app('httpRequest');

        $self->swooleRequest = $request;
        $self->server = $request->server;


        $self->method = $request->server['request_method'] ?? '';
        $self->uriPath = $request->server['request_uri'] ?? '';
        return $self;
    }


    public function get()
    {

    }

    public function post()
    {

    }

    public function input()
    {

    }
}