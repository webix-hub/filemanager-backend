--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../CommandFileSystem.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
$api->test = true;
$api->rm("D230604.txt");
$api->rm("sub1\\sub2");
$api->rm("sub1\\sub2\\zalgo.js");
$api->rm("");
$api->rm("/");

?>
--EXPECTF--
del /s {!}/D230604.txt
rd /s /q {!}/sub1/sub2
del /s {!}/sub1/sub2/zalgo.js
