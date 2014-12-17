--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../files_api.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new RealFileSystem($sandbox);
$api->debug = true;

$api->batch(array("D230604.txt","sub1\\sub2","sub1\\sub2\\zalgo.js"), array($api, "rm"));

$api->batch("D230604.txt,sub1\\sub2,sub1\\sub2\\zalgo.js", array($api, "rm"));

$api->batchSeparator = "||";
$api->batch("D230604.txt||sub1\\sub2||sub1\\sub2\\zalgo.js", array($api, "rm"));

?>
--EXPECTF--
del /s C:\http\php-files-api\tests\sandbox\D230604.txt
rd /s /q C:\http\php-files-api\tests\sandbox\sub1\sub2
del /s C:\http\php-files-api\tests\sandbox\sub1\sub2\zalgo.js
del /s C:\http\php-files-api\tests\sandbox\D230604.txt
rd /s /q C:\http\php-files-api\tests\sandbox\sub1\sub2
del /s C:\http\php-files-api\tests\sandbox\sub1\sub2\zalgo.js
del /s C:\http\php-files-api\tests\sandbox\D230604.txt
rd /s /q C:\http\php-files-api\tests\sandbox\sub1\sub2
del /s C:\http\php-files-api\tests\sandbox\sub1\sub2\zalgo.js
