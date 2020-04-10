<?php

namespace SwoCloud;

use SwoCloud\Server\Route;

class SwoCloud
{
    public function run()
    {
        (new Route())->start();
    }
}
