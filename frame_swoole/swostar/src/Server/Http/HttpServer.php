<?php

namespace SwoStar\Server\Http;

use SwoStar\Console\Input;
use SwoStar\Message\Http\Request as HttpRequest;
use SwoStar\Server\Server;

use Swoole\Http\Server as SwooleServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

/**
 * http server
 */
class HttpServer extends Server
{
    public function createServer()
    {
        var_dump($this->port, $this->host,'----http-----');
        $this->swooleServer = new SwooleServer($this->host, $this->port);
        Input::info('http server访问:http://106.13.78.8:' . $this->port);
    }

    protected function initEvent()
    {
        $this->setEvent('sub', [
            'request' => 'onRequest',
        ]);
    }

    // onRequest

    public function onRequest(SwooleRequest $request , SwooleResponse $response)
    {
        $uri = $request->server['request_uri'];
        if ($uri == '/favicon.ico') {
            $response->status(404);
            $response->end('');
            return null;
        }

        // http://127.0.0.1:9000/index

        $httpRequest = HttpRequest::init($request);

        dd($httpRequest->getMethod(), 'Method');
        dd($httpRequest->getUriPath(), 'UriPath');

        // 执行控制器/路由闭包的方法
        $return = app('route')->setMethod($httpRequest->getMethod())->match($httpRequest->getUriPath());

        $response->end($return);
    }
}



