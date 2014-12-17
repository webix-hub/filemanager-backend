--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../files_api.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new RealFileSystem($sandbox);
$api->debug = true;
$api->ls("");
$api->ls("sub1");
$api->ls("sub1/sub2");
$api->ls("sub1/../");
$api->ls("../");

?>
--EXPECTF--
List C:\http\php-files-api\tests\sandbox\
List C:\http\php-files-api\tests\sandbox\sub1\
List C:\http\php-files-api\tests\sandbox\sub1\sub2\
List C:\http\php-files-api\tests\sandbox\sub1\
List C:\http\php-files-api\tests\sandbox\
