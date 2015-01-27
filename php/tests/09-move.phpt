--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../CommandFileSystem.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
$api->debug = true;
$api->mv("D230604.txt", "sub1\\sub2");
$api->mv("sub1\\sub2", "");

?>
--EXPECTF--
move C:\http\php-files-api\tests\sandbox\D230604.txt C:\http\php-files-api\tests\sandbox\sub1\sub2
robocopy C:\http\php-files-api\tests\sandbox\sub1\sub2 C:\http\php-files-api\tests\sandbox\sub2 /e /move
