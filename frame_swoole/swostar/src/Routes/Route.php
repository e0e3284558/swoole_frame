<?php

namespace SwoStar\Routes;

use SwoStar\Console\Input;

class Route
{
    protected static $instance = null;
    // 路由本质实现时会有一个容器在存储解析之后的路由
    protected $routes = [];
    // 定义了访问的类型
    protected static $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    // 记录路由的文件地址
    protected $routeMap = [];

    // 记录请求方式
    protected $method = null;

    protected $flag = null;

    protected function __construct()
    {
        $this->routeMap = [
            'Http' => app()->getBasePath() . '/route/http.php',
            'WebSocket' => app()->getBasePath() . '/route/web_socket.php'
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

    public function wsController($uri, $controller)
    {
        $actions = [
            'open',
            'message',
            'close'
        ];
        foreach ($actions as $key => $action) {
            $this->addRoute([$action], $uri, $controller . '@' . $action);
        }
    }

    /**
     * 注册路由
     * @param array $methods
     * @param $uri
     * @param $action
     * @return $this
     */
    public function addRoute($methods, $uri, $action)
    {
        foreach ($methods as $method) {
            $this->routes[$this->flag][$method][$uri] = $action;
        }
        return $this;
    }

    /**
     * 根据请求校验路由，并执行方法
     */
    public function match($path, $param = [])
    {
        // 1. 获取请求的uriPath
        // 2. 根据类型获取路由
        // 3. 判断请求的uri匹配相应路由，返回action
        // 4. 判断控制器还是闭包，分别执行
        $action = null;
        dd($this->method);
        dd($this->routes);
        dd($this->routes[$this->method]);
        foreach ($this->routes[$this->flag][$this->method] as $uri => $value) {
            $uri = ($uri && substr($uri, 0, 1) != '/') ? '/' . $uri : $uri;
            dd($uri, "这是处理的url");
            dd($path, "这是访问的路径");
            if ($path === $uri) {
                $action = $value;
                break;
            }
        }
        if (!empty($action)) {
            return $this->runAction($action,$param);
        }
        Input::info('没有找到方法');
        return 404;
    }

    private function runAction($action, $param = null)
    {
        if ($action instanceof \Closure) {
            return $action(...$param);
        } else {
            // 控制器解析
            $namespace = "\App\\" . $this->flag . "\\Controller\\";
            $arr = explode('@', $action);
            $controller = $namespace . $arr[0];
            $class = new $controller();
            return $class->{$arr[1]}(...$param);
        }
    }



    public function registeRoute()
    {
        foreach ($this->routeMap as $key => $path) {
            $this->flag = $key;
            require_once $path;
        }
        return $this;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function setFlag($flag)
    {
        $this->flag=$flag;
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
