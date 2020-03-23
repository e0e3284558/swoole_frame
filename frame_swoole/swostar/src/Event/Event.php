<?php

namespace SwoStar\Event;

class Event
{
    protected $events = [];

    /**
     * 事件注册
     * @param string $event
     * @param Closure $callback
     */
    public function register($event, $callback)
    {
        $event = strtolower($event);
        //判断事件是否存在
//        if (){
//
//        }
        $this->events[$event] = ['callback' => $callback];
    }

    /**
     * 事件触发函数
     * @param string $event
     * @param array $param
     * @return bool
     */
    public function trigger($event, $param = [])
    {
        $event = strtolower($event);
        if (isset($this->events[$event])) {
            ($this->events[$event]['callback'])(...$param);
            dd('事件执行成功');
            return true;
        }
        dd('事件不存在');

    }

    public function getEvents($event = null)
    {
        return empty($event) ? $this->events : $this->events[$event];
    }
}