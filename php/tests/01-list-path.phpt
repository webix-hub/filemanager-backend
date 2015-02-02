--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../CommandFileSystem.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
$api->test = true;
$api->ls("");
$api->ls("sub1");
$api->ls("sub1/sub2");
$api->ls("sub1/../");
$api->ls("../");

?>
--EXPECTF--
List {!}/
List {!}/sub1/
List {!}/sub1/sub2/
List {!}/sub1/
List {!}/
