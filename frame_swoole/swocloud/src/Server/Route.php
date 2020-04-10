<?php

namespace SwoCloud\Server;


use Swoole\Server as SwooleServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use SwoStar\Console\Input;
use Swoole\Coroutine\Http\Client;
use \Redis;

/**
 * 1. 检测IM-server的存活状态
 * 2. 支持权限认证
 * 3. 根据服务器的状态，按照一定的算法，计算出该客户端连接到哪台IM-server，返回给客户端，客户端再去连接到对应的服务端,保存客户端与IM-server的路由关系
 * 4. 如果 IM-server宕机，会自动从Redis中当中剔除
 * 5. IM-server上线后连接到Route，自动加 入Redis(im-server ip:port)
 * 6. 可以接受来自PHP代码、C++程序、Java程序的消息请求，转发给用户所在的IM-server
 * 7. 缓存服务器地址，多次查询redis
 *
 * 是一个websocket
 */
class Route extends Server
{
    protected $dispatcher = null;

    protected $reids = null;

    protected $serverKey = 'im_server';

    protected $arithmetic = 'round';


    public function onWorkerStart(SwooleServer $server, int $worker_id)
    {
        $this->redis = new Redis();
        $this->redis->pconnect('127.0.0.1', 6379);
        $this->redis->auth('bifei970827...');
    }


    public function onOpen(SwooleServer $server, $request)
    {
        dd("onOpen");
    }

    public function onMessage(SwooleServer $server, $frame)
    {
//        dd('onMessage');
        $data = json_decode($frame->data, true);
        $fd = $frame->fd;
        $this->getDispatcher()->{$data['method']}($this, $server, ...[$fd, $data]);
    }

    public function onClose(SwooleServer $server, $fd)
    {
        dd('onClose');
    }

    public function onRequest(SwooleRequest $request, SwooleResponse $response)
    {
        if ($request->server['request_uri'] == '/favicon.ico') {
            $response->status(404);
            $request->end('');
            return null;
        }
        // 解决跨域
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', 'GET,POST');

        /**
         *
         * [
         *      'method'=>'login',
         * ]
         */
        $this->getDispatcher()->{$request->post['method']}($this, $request, $response);
    }

    /**
     * 指定为某一个链接的服务器发送信息
     * @param $ip
     * @param $port
     * @param $data
     * @param null $header
     */
    public function send($ip, $port, $data, $header = null)
    {
        dd('发送');
        // 携带任务id
        $unipid = session_create_id();
        $data['msg_id'] = $unipid;

        $cli = new Client($ip, $port);

        empty($header)?:$cli->setHeaders($header);

        if ($cli->upgrade('/')) {
            $cli->push(\json_encode($data));
        }

        // 发送成功之后调用 是否确认接收
        $this->confirmGo($unipid, $data, $cli);
    }

    public function createServer()
    {
        $this->swooleServer = new SwooleWebSocketServer($this->host, $this->port);
        Input::info('webSocket server访问:wx://106.13.78.8:' . $this->port);
    }

    protected function initEvent()
    {
        $this->setEvent('sub', [
            'request' => 'onRequest',
            'open' => 'onOpen',
            'message' => 'onMessage',
            'close' => 'onClose'
        ]);
    }

    public function getDispatcher()
    {
        if (empty($this->dispatcher)) {
            $this->dispatcher = new Dispatcher();
        }
        return $this->dispatcher;
    }

    /**
     * 获取所有服务器的信息，可用可连接的
     * @return array
     */
    public function getIMServers()
    {
        return $this->getRedis()->smembers($this->getServerKey());
    }

    /**
     * @return Redis mixed
     */
    public function getRedis()
    {
        return $this->redis;
    }

    public function getServerKey()
    {
        return $this->serverKey;
    }

    public function getArithmetic()
    {
        return $this->arithmetic;
    }

    public function setArithmetic($arithmetic)
    {
        $this->arithmetic = $arithmetic;
        return $this;
    }


}