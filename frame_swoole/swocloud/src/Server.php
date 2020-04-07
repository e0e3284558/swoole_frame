<?php

namespace SwoCloud;

use Swoole\Server as SwooleServer;
use Swoole\Coroutine\Http\Client;


/**
 * 所有服务的父类， 写一写公共的操作
 */
abstract class Server
{
    // 属性
    /**
     * [protected description]
     * @var Swoole/Server
     */
    protected $swooleServer;

    protected $port = 9500;

    protected $host = '0.0.0.0';

    protected $watchFile = false;

    protected $config = [
        'task_worker_num' => 0,
    ];
    /**
     * 注册的回调事件
     * [
     *   // 这是所有服务均会注册的时间
     *   "server" => [],
     *   // 子类的服务
     *   "sub" => [],
     *   // 额外扩展的回调函数
     *   "ext" => []
     * ]
     *
     * @var array
     */
    protected $event = [
        // 这是所有服务均会注册的时间
        'server' => [
            // 事件   =》 事件函数
            'start' => 'onStart',
            'managerStart' => 'onManagerStart',
            'managerStop' => 'onManagerStop',
            'shutdown' => 'onShutdown',
            'workerStart' => 'onWorkerStart',
            'workerStop' => 'onWorkerStop',
            'workerError' => 'onWorkerError',
        ],
        // 子类的服务
        'sub' => [],
        // 额外扩展的回调函数
        // 如 ontart等
        'ext' => []
    ];

    public function __construct()
    {
        // 1. 创建 swoole server
        $this->createServer();
        // 3. 设置需要注册的回调函数
        $this->initEvent();
        // 4. 设置swoole的回调函数
        $this->setSwooleEvent();
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
        $cli = new Client($ip, $port);
        empty($header)?:$cli->setHeaders($header);

        if ($cli->upgrade('/')){
            $cli->push(json_encode($data));
        }
    }
    /**
     * 创建服务
     */
    protected abstract function createServer();

    /**
     * 初始化监听的事件
     */
    protected abstract function initEvent();

    // 通用的方法

    public function start()
    {
        // 2. 设置配置信息
        $this->swooleServer->set($this->config);
        // 5. 启动
        $this->swooleServer->start();
    }


    /**
     * 设置swoole的回调事件
     */
    protected function setSwooleEvent()
    {
        foreach ($this->event as $type => $events) {
            foreach ($events as $event => $func) {
                $this->swooleServer->on($event, [$this, $func]);
            }
        }
    }


    // 回调方法
    public function onStart(SwooleServer $server)
    {

    }

    public function onManagerStart(SwooleServer $server)
    {

    }

    public function onManagerStop(SwooleServer $server)
    {

    }

    public function onShutdown(SwooleServer $server)
    {

    }

    public function onWorkerStart(SwooleServer $server, int $worker_id)
    {

    }

    public function onWorkerStop(SwooleServer $server, int $worker_id)
    {

    }

    public function onWorkerError(SwooleServer $server, int $workerId, int $workerPid, int $exitCode, int $signal)
    {
    }

    // GET | SET

    /**
     * @param array
     *
     * @return static
     */
    public function setEvent($type, $event)
    {
        // 暂时不支持直接设置系统的回调事件
        if ($type == "server") {
            return $this;
        }
        $this->event[$type] = $event;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return static
     */
    public function setConfig($config)
    {
        $this->config = array_map($this->config, $config);
        return $this;
    }

    public function watchFile($watchFile)
    {
        $this->watchFile = $watchFile;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }
}
