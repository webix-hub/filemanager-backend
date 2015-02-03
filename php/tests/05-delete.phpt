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
$api->rm("D230604.txt");
$api->rm("sub1\\sub2");
$api->rm("sub1\\sub2\\zalgo.js");
$api->rm("");
$api->rm("/");

$api = new PHPFileSystem($sandbox);
$api->test = true;
$api->rm("D230604.txt");
$api->rm("sub1\\sub2");
$api->rm("sub1\\sub2\\zalgo.js");
$api->rm("");
$api->rm("/");

$filesystem = new Filesystem(new Adapter($sandbox));
$api = new LocalFlyFileSystem($filesystem, $sandbox);
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
Delete {!}/D230604.txt
Delete {!}/sub1/sub2
Delete {!}/sub1/sub2/zalgo.js
Delete {!}/D230604.txt
Delete {!}/sub1/sub2
Delete {!}/sub1/sub2/zalgo.js
