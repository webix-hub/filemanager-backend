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

$api->batch(array("D230604.txt" , "sub1\\sub2"),  array($api, "cp"), "sub1");

$api->batch("D230604.txt,sub1\\sub2", array($api, "cp"), "sub1");

$api->batchSeparator = "|||";
$api->batch("D230604.txt|||sub1\\sub2", array($api, "cp"), "sub1");

$api = new PHPFileSystem($sandbox);
$api->test = true;

$api->batch(array("D230604.txt" , "sub1\\sub2"),  array($api, "cp"), "sub1");

$api->batch("D230604.txt,sub1\\sub2", array($api, "cp"), "sub1");

$api->batchSeparator = "|||";
$api->batch("D230604.txt|||sub1\\sub2", array($api, "cp"), "sub1");

$filesystem = new Filesystem(new Adapter($sandbox));
$api = new LocalFlyFileSystem($filesystem, $sandbox);
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
Copy ({!}/D230604.txt | {!}/sub1)
Copy ({!}/sub1/sub2 | {!}/sub1)
Copy ({!}/D230604.txt | {!}/sub1)
Copy ({!}/sub1/sub2 | {!}/sub1)
Copy ({!}/D230604.txt | {!}/sub1)
Copy ({!}/sub1/sub2 | {!}/sub1)
Copy ({!}/D230604.txt | {!}/sub1)
Copy ({!}/sub1/sub2 | {!}/sub1)
Copy ({!}/D230604.txt | {!}/sub1)
Copy ({!}/sub1/sub2 | {!}/sub1)
Copy ({!}/D230604.txt | {!}/sub1)
Copy ({!}/sub1/sub2 | {!}/sub1)
