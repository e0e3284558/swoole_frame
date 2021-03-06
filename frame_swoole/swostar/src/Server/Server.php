<?php

namespace SwoStar\Server;

use SwoStar\Console\Input;
use SwoStar\RPC\Rpc;
use SwoStar\Supper\Inotify;
use Swoole\Server as SwooleServer;
use SwoStar\Foundation\Application;
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

    protected $app;

    protected $inotify = null;

    protected $port = null;

    protected $host = null;

    protected $watchFile = false;

    protected $redis;

    // 记录系统pid的信息
    protected $pidFile = '/runtime/swostar.pid';

    protected $config = [
        'task_worker_num' => 0,
    ];
    /**
     * 用于记录pid的信息
     * @var array
     */
    protected $pidMap = [
        'masterPid' => 0,
        'managerPid' => 0,
        'workerPids' => [],
        'taskPids' => []
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
        "server" => [
            // 事件   =》 事件函数
            "start" => "onStart",
            "managerStart" => "onManagerStart",
            "managerStop" => "onManagerStop",
            "shutdown" => "onShutdown",
            "workerStart" => "onWorkerStart",
            "workerStop" => "onWorkerStop",
            "workerError" => "onWorkerError",
        ],
        // 子类的服务
        "sub" => [],
        // 额外扩展的回调函数
        // 如 ontart等
        "ext" => []
    ];

    public function __construct(Application $app, $flag = 'http')
    {
        $this->app = $app;
        $this->flag = $flag;

        $this->initSetting();
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
        $config = app('config');
        // 2. 设置配置信息
        $this->swooleServer->set($this->config);

        if (app('config')->get('server.http.tcpable')) {
            new Rpc($this->swooleServer, $config->get('server.http.rpc'));
        }

        // 5. 启动
        $this->swooleServer->start();
    }

//    public function initSetting()
//    {
//        $config = app('config');
//        $this->port = $config->get('server.http.port');
//        $this->host = $config->get('server.http.host');
//        var_dump('初始化端口', $this->host, $this->port);
//
//    }

    protected abstract function initSetting();

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

    protected function watchEvent()
    {
        return function ($event) {
            $action = 'file:';
            switch ($event['mask']) {
                case IN_CREATE:
                    $action = 'IN_CREATE';
                    break;

                case IN_DELETE:
                    $action = 'IN_DELETE';
                    break;
                case \IN_MODIFY:
                    $action = 'IN_MODIF';
                    break;
                case \IN_MOVE:
                    $action = 'IN_MOVE';
                    break;
            }
            $this->swooleServer->reload();
        };
    }

    // 回调方法
    public function onStart(SwooleServer $server)
    {
        $this->pidMap['masterPid'] = $server->master_pid;
        $this->pidMap['managerPid'] = $server->manager_pid;

        // 保存PID到文件里面
        $pidStr = \sprintf('%s,%s', $server->master_pid, $server->manager_pid);
        \file_put_contents(app()->getBasePath() . $this->pidFile, $pidStr);
        if ($this->watchFile) {
            $this->inotify = new Inotify($this->app->getBasePath(), $this->watchEvent());
            $this->inotify->start();
        }
        // 设置启动事件
        $this->app->make('event')->trigger('start', [$this, $server]);
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
        $this->pidMap['workerPids'] = [
            'id' => $worker_id,
            'pid' => $server->worker_id
        ];
        $this->redis = new \Redis();
        $this->redis->pconnect("127.0.0.1", 6379);
        $this->redis->auth('bifei970827...');

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

    public function getPort()
    {
        return $this->port;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function watchFile($watchFile)
    {
        $this->watchFile = $watchFile;
    }

    public function getRedis()
    {
        return $this->redis;
    }
}
