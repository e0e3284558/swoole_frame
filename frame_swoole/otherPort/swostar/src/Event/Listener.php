<?php

namespace SwoStar\Event;

abstract class Listener
{
    protected $name = '';

    public abstract function handler();

    public function getName()
    {
        return $this->name;
    }
}