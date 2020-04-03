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
        $this->swooleServer = new SwooleServer($this->host, $this->port);

        Input::info('http server 访问 : http://192.168.186.130:'.$this->port );
    }
    // 初始化默认设置
    protected function initSetting()
    {
        $config = app('config');
        $this->port = $config->get('server.http.port');
        $this->host = $config->get('server.http.host');
        $this->config = $config->get('server.http.swoole');
    }
    protected function initEvent(){
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
            return null;
        }

        // http://127.0.0.1:9000/index

        $httpRequest = HttpRequest::init($request);

        // dd($httpRequest->getMethod(), "Method");
        // dd($httpRequest->getUriPath(), "UriPath"); //   /index

        // 执行控制器的方法
        $return = app('route')->setFlag('Http')->setMethod($httpRequest->getMethod())->match($httpRequest->getUriPath());

        $response->end($return);
    }
}
