<?php

namespace App\Listener;

use SwoStar\Event\Listener;

class StartListener extends Listener
{
    protected $name='start';

    public function handler()
    {
        dd('this is StartListener handler','StartListener');
    }
}