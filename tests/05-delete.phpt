--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../files_api.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new RealFileSystem($sandbox);
$api->debug = true;
$api->rm("D230604.txt");
$api->rm("sub1\\sub2");
$api->rm("sub1\\sub2\\zalgo.js");

?>
--EXPECTF--
del /s C:\http\php-files-api\tests\sandbox\D230604.txt
rd /s /q C:\http\php-files-api\tests\sandbox\sub1\sub2
del /s C:\http\php-files-api\tests\sandbox\sub1\sub2\zalgo.js
