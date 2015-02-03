--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../CommandFileSystem.php";
include_once dirname(__FILE__) . "/../PHPFileSystem.php";
include_once dirname(__FILE__) . "/../FlyFileSystem.php";

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
$api->test = true;

$api->batch(array("D230604.txt","sub1\\sub2","sub1\\sub2\\zalgo.js"), array($api, "rm"));

$api->batch("D230604.txt,sub1\\sub2,sub1\\sub2\\zalgo.js", array($api, "rm"));

$api->batchSeparator = "||";
$api->batch("D230604.txt||sub1\\sub2||sub1\\sub2\\zalgo.js", array($api, "rm"));

$api = new PHPFileSystem($sandbox);
$api->test = true;

$api->batch(array("D230604.txt","sub1\\sub2","sub1\\sub2\\zalgo.js"), array($api, "rm"));

$api->batch("D230604.txt,sub1\\sub2,sub1\\sub2\\zalgo.js", array($api, "rm"));

$api->batchSeparator = "||";
$api->batch("D230604.txt||sub1\\sub2||sub1\\sub2\\zalgo.js", array($api, "rm"));

$filesystem = new Filesystem(new Adapter($sandbox));
$api = new LocalFlyFileSystem($filesystem, $sandbox);
$api->test = true;

$api->batch(array("D230604.txt","sub1\\sub2","sub1\\sub2\\zalgo.js"), array($api, "rm"));

$api->batch("D230604.txt,sub1\\sub2,sub1\\sub2\\zalgo.js", array($api, "rm"));

$api->batchSeparator = "||";
$api->batch("D230604.txt||sub1\\sub2||sub1\\sub2\\zalgo.js", array($api, "rm"));
?>
--EXPECTF--
del /s {!}/D230604.txt
rd /s /q {!}/sub1/sub2
del /s {!}/sub1/sub2/zalgo.js
del /s {!}/D230604.txt
rd /s /q {!}/sub1/sub2
del /s {!}/sub1/sub2/zalgo.js
del /s {!}/D230604.txt
rd /s /q {!}/sub1/sub2
del /s {!}/sub1/sub2/zalgo.js
Delete {!}/D230604.txt
Delete {!}/sub1/sub2
Delete {!}/sub1/sub2/zalgo.js
Delete {!}/D230604.txt
Delete {!}/sub1/sub2
Delete {!}/sub1/sub2/zalgo.js
Delete {!}/D230604.txt
Delete {!}/sub1/sub2
Delete {!}/sub1/sub2/zalgo.js
Delete {!}/D230604.txt
Delete {!}/sub1/sub2
Delete {!}/sub1/sub2/zalgo.js
Delete {!}/D230604.txt
Delete {!}/sub1/sub2
Delete {!}/sub1/sub2/zalgo.js
Delete {!}/D230604.txt
Delete {!}/sub1/sub2
Delete {!}/sub1/sub2/zalgo.js
