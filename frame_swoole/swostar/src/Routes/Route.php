<?php

namespace SwoStar\Routes;

class Route
{
    protected static $instance = null;
    // 路由本质实现时会有一个容器在存储解析之后的路由
    protected $routes = [];
    // 定义了访问的类型
    protected static $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    // 记录路由的文件地址
    protected $routeMap = [];

    protected function __construct()
    {
        $this->routeMap = [
            'Http' => app()->getBasePath() . '/route/http.php'
        ];
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function get($uri, $action)
    {
        return $this->addRoute(['GET'], $uri, $action);
    }

    public function post($uri, $action)
    {
        return $this->addRoute(['POST'], $uri, $action);
    }

    public function any($uri, $action)
    {
        return $this->addRoute(self::$verbs, $uri, $action);
    }

    /**
     * 注册路由
     * @param $methods
     * @param $uri
     * @param $action
     * @return $this
     */
    public function addRoute($methods, $uri, $action)
    {
        foreach ($methods as $method) {
            $this->routes[$method][$uri] = $action;
        }
        return $this;
    }

    /**
     * 根据请求校验路由，并执行方法
     */
    public function match()
    {

    }

    public function registeRoute()
    {
        foreach ($this->routeMap as $key => $path) {
            require_once $path;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
