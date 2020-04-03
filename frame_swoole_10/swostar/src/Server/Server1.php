<?php
namespace SwoStar\Server;

use Swoole\Server as SwooleServer;
/**
 * 服务的父级类
 */
abstract class Server
{
    protected $mod = SWOOLE_PROCESS;
    
    protected $sock_type = SWOOLE_SOCK_TCP;
    /**
     * swostar server
     * @var Server|HttpServer|WebSocketServer|
     */
    protected $swooleServer;
    /**
     * @var SwoStar/Support/Inotify
     */
    protected $inotify = null;
    /**
     * 服务的类型
     * @var tcp|udp|http|ws
     */
    protected $serverType = 'TCP';
    /**
     * 是否开启文件检测
     * @var bool
     */
    protected $watchFile = false;
    /**
     * 监听端口
     * @var int
     */
    protected $port = 9000;
    /**
     * 监听的端口
     * @var string
     */
    protected $host = '0.0.0.0';
    /**
     * 用于记录系统pid的信息
     * @var string
     */
    protected $pidFile = "/runtime/swostar.pid";
    /**
     * 用于记录pid的信息
     * @var array
     */
    protected $pidMap = [
        'masterPid'  => 0,
        'managerPid' => 0,
        'workerPids' => [],
        'taskPids'   => []
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
            "start"        => "onStart",
            "managerStart" => "onManagerStart",
            "managerStop"  => "onManagerStop",
            "shutdown"     => "onShutdown",
            "workerStart"  => "onWorkerStart",
            "workerStop"   => "onWorkerStop",
            "workerError"  => "onWorkerError",
        ],
        // 子类的服务
        "sub" => [],
        // 额外扩展的回调函数
        // 如 ontart等
        "ext" => []
    ];
    /**
     * swoole的相关配置信息
     * @var array
     */
    protected $config = [
        'task_worker_num' => 0,
    ];

    /**
     * 创建服务
     * 六星教育 @shineyork老师
     */
    protected abstract function createServer();
    /**
     * 初始化监听的事件
     * 六星教育 @shineyork老师
     */
    protected abstract function initEvent();

    public function __construct(Application $app)
    {
        $this->app = $app;
        // 创建服务
        $this->createServer();
        // 设置回调函数
        $this->initEvent();
        // 设置swoole的回调事件
        $this->setSwooleEvent();
    }

    /**
     * 启动服务
     * 六星教育 @shineyork老师
     */
    public function start()
    {
        if (empty($this->swooleServer)) {
            // 应该 return error;
            return "error";
        }
        // 设置swoole的配置
        $this->swooleServer->set($this->config);

        // 启动swoole服务
        $this->swooleServer->start();
    }
    /**
     * 设置swoole的回调事件
     * 六星教育 @shineyork老师
     */
    protected function setSwooleEvent()
    {
        foreach ($this->event as $type => $events) {
            foreach ($events as $event => $func) {
                $this->swooleServer->on($event, [$this, $func]);
            }
        }
    }
    /**
     * 代码热加载
     * 六星教育 @shineyork老师
     */
    public function watchEvent()
    {

    }

    // 注册回调函数

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
        $this->pidMap['workerPids'] = [
            'id'  => $worker_id,
            'pid' => $server->worker_id
        ];
    }
    public function onWorkerStop(SwooleServer $server, int $worker_id)
    {

    }
    public function onWorkerError(SwooleServer $server, int $workerId, int $workerPid, int $exitCode, int $signal)
    {

    }

    // get | set 方法

    public function setWatchFile($watch)
    {
        $this->watchFile = $watch;
        return $this;
    }

    public function getServerType()
    {
        return $this->serverType;
    }

    public function setServerType($serverType)
    {
        $this->serverType = $serverType;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     *
     * @return static
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return static
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return array
     */
    public function getEvent(): array
    {
        return $this->swooleEvent;
    }

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
        return $this->swooleConfig;
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
}
