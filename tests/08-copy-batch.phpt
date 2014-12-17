--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../files_api.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new RealFileSystem($sandbox);
$api->debug = true;

$api->batch(array("D230604.txt" , "sub1\\sub2"),  array($api, "cp"), "sub1");

$api->batch("D230604.txt,sub1\\sub2", array($api, "cp"), "sub1");

$api->batchSeparator = "|||";
$api->batch("D230604.txt|||sub1\\sub2", array($api, "cp"), "sub1");


?>
--EXPECTF--
copy C:\http\php-files-api\tests\sandbox\D230604.txt C:\http\php-files-api\tests\sandbox\sub1
robocopy C:\http\php-files-api\tests\sandbox\sub1\sub2 C:\http\php-files-api\tests\sandbox\sub1\sub2 /e
copy C:\http\php-files-api\tests\sandbox\D230604.txt C:\http\php-files-api\tests\sandbox\sub1
robocopy C:\http\php-files-api\tests\sandbox\sub1\sub2 C:\http\php-files-api\tests\sandbox\sub1\sub2 /e
copy C:\http\php-files-api\tests\sandbox\D230604.txt C:\http\php-files-api\tests\sandbox\sub1
robocopy C:\http\php-files-api\tests\sandbox\sub1\sub2 C:\http\php-files-api\tests\sandbox\sub1\sub2 /e
