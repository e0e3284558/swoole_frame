<?php
namespace Bifei\Io\Reactor\Event\Single;

use \Event as Event;
use \EventBase as EventBase;

class Reactor
{
    protected $reactor;

    protected $events;

    public static $instance = null;

    const READ = Event::READ | Event::PERSIST;

    const WRITE = Event::WRITE | Event::PERSIST;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();

            self::$instance->reactor = new EventBase;
        }
        return self::$instance;
    }

    public function add($fd, $what, $cb, $arg = null)
    {

        switch ($what) {
            case self::READ:
                $event = new Event($this->reactor, $fd, self::READ, $cb, $arg);
                break;
            case self::WRITE:
                $event = new Event($this->reactor, $fd, self::WRITE, $cb, $arg);
                break;
            default:
                $event = new Event($this->reactor, $fd, $what, $cb, $arg);
                break;
        }

        $event->add();
        $this->events[(int) $fd][$what] = $event;
    }

    public function del($fd, $what = 'all')
    {
        $events = $this->events[(int) $fd];
        if ($what == 'all') {
            foreach ($events as $event) {
                $event->free();
            }
        } else {
            if ($what != self::READ && $what != self::WRITE) {
                throw new \Exception('不存在的事件');
            }

            $events[$what]->free();
        }
    }

    public function run()
    {
        $this->reactor->loop();
    }

    public function stop()
    {
        foreach ($this->events as $events) {
            foreach ($events as $event) {
                $event->free();
            }
        }
        $this->reactor->stop();
    }
}
