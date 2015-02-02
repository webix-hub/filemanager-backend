--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../CommandFileSystem.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
$api->test = true;

$api->batch(array("D230604.txt" , "sub1\\sub2"),  array($api, "cp"), "sub1");

$api->batch("D230604.txt,sub1\\sub2", array($api, "cp"), "sub1");

$api->batchSeparator = "|||";
$api->batch("D230604.txt|||sub1\\sub2", array($api, "cp"), "sub1");


?>
--EXPECTF--
copy {!}/D230604.txt {!}/sub1
robocopy {!}/sub1/sub2 {!}/sub1/sub2 /e
copy {!}/D230604.txt {!}/sub1
robocopy {!}/sub1/sub2 {!}/sub1/sub2 /e
copy {!}/D230604.txt {!}/sub1
robocopy {!}/sub1/sub2 {!}/sub1/sub2 /e
