<?php
require __DIR__.'/../vendor/autoload.php';
use SwoStar\Index;
use SwoStar\Foundation\Application;
use App\App;

echo (new Index())->index()."\n";
echo (new App())->index();

echo app('index')->index();
