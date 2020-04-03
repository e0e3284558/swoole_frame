<?php
namespace SwoStar\Event;

abstract class Listener
{
    protected $name = 'listener';

    public abstract function handler();

    public function getName()
    {
        return $this->name;
    }
}
