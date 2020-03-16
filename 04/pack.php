<?php

$test = 'hello world';
var_dump(strlen($test));
$len = pack("N", strlen($test));
//$r = $len . $test;
var_dump($len);
var_dump(unpack("N", $len));

