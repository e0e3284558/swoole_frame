<?php
use SwoStar\Routes\Route;

// http路由
Route::get('index', function () {
    return "this is route index () tests";
});

Route::get('/index/dd', 'IndexController@dd');