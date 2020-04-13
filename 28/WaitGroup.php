<?php

class WaitGroup
{
    protected $chan;

    protected $count;

    public function __construct()
    {
        $this->chan = new chan();
    }


    public function add()
    {
        $this->count++;
    }

    public function push($data)
    {
        $this->chan->push($data);
    }

    public function wait()
    {
        $return = [];
        for ($i = 0; $i < 2; $i++) {
            $return[] = $this->chan->pop();
        }
        return $return;
    }
}