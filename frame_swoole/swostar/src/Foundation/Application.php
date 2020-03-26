<?php

namespace SwoStar\Foundation;

use SwoStar\Config\Config;
use SwoStar\Container\Container;
use SwoStar\Event\Event;
use SwoStar\Index;
use SwoStar\Message\Http\Request;
use SwoStar\Routes\Route;
use SwoStar\Server\Http\HttpServer;
use SwoStar\Server\WebSocket\WebSocketServer;

class Application extends Container
{
    protected const SWOSTAR_WELCOME = "
      _____                     _____     ___
     /  __/             ____   /  __/  __/  /__   ___ __    __  __
     \__ \  | | /| / / / __ \  \__ \  /_   ___/  /  _`  |  |  \/ /
     __/ /  | |/ |/ / / /_/ /  __/ /   /  /_    |  (_|  |  |   _/
    /___/   |__/\__/  \____/  /___/    \___/     \___/\_|  |__|
    ";

    protected $basePath = "";

    public function __construct($path = null)
    {
        if (!empty($path)) {
            $this->setBasePath($path);
        }
        $this->registerBaseBindings();
        $this->init();
        dd(self::SWOSTAR_WELCOME, '启动项目');
    }

    public function run($argv)
    {
        switch ($argv[1]) {
            case 'http:start':
                $server = new HttpServer($this);
                break;
            case 'ws:start':
                $server = new WebSocketServer($this);
                break;
            default:
                dd('请输入正确的参数  http:start or ws:start');
                return null;
        }
        // php bin/swostar ws:start
        $server->watchFile(true);
        $server->start();
    }

    public function registerBaseBindings()
    {
        self::setInstance($this);
        $binds = [
            // 标识=>对象
            'index' => (new Index()),
            'httpRequest' => (new Request()),
            'config'      => (new Config()),
        ];
        foreach ($binds as $key => $value) {
            $this->bind($key, $value);
        }

    }

    public function init()
    {
        $this->bind('route', Route::getInstance()->registeRoute());
        $this->bind('event', $this->registerEvent());

        dd($this->make('event')->getEvents());
    }

    /**
     * 注册框架事件
     *
     * @return Event
     */
    public function registerEvent()
    {
        $event = new Event();
        $files = scandir($this->getBasePath() . '/app/Listener/');
        foreach ($files as $key => $file) {
            if ($file === '.' || $file === '..' || $files === '.DS_Store') {
                continue;
            }
            $class = 'App\\Listener\\' . explode('.', $file)[0];
            if (class_exists($class)) {
                $listener = new $class($this);
                $event->register($listener->getName(), [$listener, 'handler']);
            }
        }
        return $event;
    }


    public function setBasePath($path)
    {
        $this->basePath = \rtrim($path, '\/');
    }

    public function getBasePath()
    {
        return $this->basePath;
    }
}
