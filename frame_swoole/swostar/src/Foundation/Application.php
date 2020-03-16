<?php

namespace SwoStar\Foundation;

use SwoStar\Config\Config;
use SwoStar\Container\Container;
use SwoStar\Index;
use SwoStar\Message\Http\Request;
use SwoStar\Routes\Route;
use SwoStar\Server\Http\HttpServer;

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

    public function run()
    {
        $httpServer = new HttpServer($this);
//        $httpServer->watchFile(true);
        $httpServer->start();
    }

    public function registerBaseBindings()
    {
        self::setInstance($this);
        $binds = [
            // 标识=>对象
            'index' => (new Index()),
            'httpRequest' => (new Request()),
            'config' => (new Config())
        ];
        foreach ($binds as $key => $value) {
            $this->bind($key, $value);
        }
    }

    public function init()
    {
        $this->bind('route', Route::getInstance()->registeRoute());
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
