<?php

namespace SwoStar\Server\Http;

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
        $this->swooleServer = new SwooleServer($this->host, $this->port);
    }

    protected function initEvent()
    {
        $this->setEvent('sub', [
            'request' => 'onRequest',
        ]);
    }

    // onRequest

    public function onRequest(SwooleRequest $request, SwooleResponse $response)
    {
        $uri = $request->server['request_uri'];
        if ($uri == '/favicon.ico') {
            $response->status(404);
            $response->end('');
        }
        $httpRequest = HttpRequest::init($request);
        dd($httpRequest->getMethod(),'Method');
        dd($httpRequest->getUriPath(),'UriPath');
        $response->end("<h1>Hello swostar</h1>");
    }
}



