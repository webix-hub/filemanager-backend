--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../CommandFileSystem.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
$api->debug = true;
$api->rm("D230604.txt");
$api->rm("sub1\\sub2");
$api->rm("sub1\\sub2\\zalgo.js");
$api->rm("");
$api->rm("/");

?>
--EXPECTF--
del /s C:\http\php-files-api\tests\sandbox\D230604.txt
rd /s /q C:\http\php-files-api\tests\sandbox\sub1\sub2
del /s C:\http\php-files-api\tests\sandbox\sub1\sub2\zalgo.js
