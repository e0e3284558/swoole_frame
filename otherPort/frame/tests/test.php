<?php
require __DIR__.'/../vendor/autoload.php';
use SwoStar\Index;
use App\App;
use SwoStar\Foundation\Application;

echo (new Index())->index()."\n";
echo (new App())->index();

//echo Application::getInstance()->make('index')->index();
echo app('index')->index();