--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../files_api.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new RealFileSystem($sandbox);
$api->debug = true;
$api->cp("D230604.txt", "sub1\\sub2");
$api->cp("sub1\\sub2", "");

?>
--EXPECTF--
copy C:\http\php-files-api\tests\sandbox\D230604.txt C:\http\php-files-api\tests\sandbox\sub1\sub2
robocopy C:\http\php-files-api\tests\sandbox\sub1\sub2 C:\http\php-files-api\tests\sandbox\ /e
