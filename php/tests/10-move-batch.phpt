--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../CommandFileSystem.php";
include_once dirname(__FILE__) . "/../PHPFileSystem.php";
include_once dirname(__FILE__) . "/../FlyFileSystem.php";
include_once dirname(__FILE__) . "/../DBFileSystem.php";

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
$api->test = true;

$api->batch(array("D230604.txt" , "sub1\\sub2"),  array($api, "mv"), "sub1");

$api->batch("D230604.txt,sub1\\sub2", array($api, "mv"), "sub1");

$api->batchSeparator = "|||";
$api->batch("D230604.txt|||sub1\\sub2", array($api, "mv"), "sub1");

$api = new PHPFileSystem($sandbox);
$api->test = true;

$api->batch(array("D230604.txt" , "sub1\\sub2"),  array($api, "mv"), "sub1");

$api->batch("D230604.txt,sub1\\sub2", array($api, "mv"), "sub1");

$api->batchSeparator = "|||";
$api->batch("D230604.txt|||sub1\\sub2", array($api, "mv"), "sub1");

$filesystem = new Filesystem(new Adapter($sandbox));
$api = new LocalFlyFileSystem($filesystem, $sandbox);
$api->test = true;

$api->batch(array("D230604.txt" , "sub1\\sub2"),  array($api, "mv"), "sub1");

$api->batch("D230604.txt,sub1\\sub2", array($api, "mv"), "sub1");

$api->batchSeparator = "|||";
$api->batch("D230604.txt|||sub1\\sub2", array($api, "mv"), "sub1");

$config = array('engine' => 'sqlite', 'database' => 'base.sqlite3');
$config_folders = array(
    'type' => 'folders',
    'table_name' => 'folders',
    'structure' => array(
        'id' => 'id',
        'value' => 'value',
        'folder_id' => 'folder_id'
    )
);
$config_files = array(
    'type' => 'files',
    'table_name' => 'files',
    'structure' => array(
        'id' => 'id',
        'value' => 'value',
        'folder_id' => 'folder_id'
    )
);

$api = new DBFileSystem($config, $config_folders, $config_files);
$api->test = true;
$api->batch(array(array(3, 1, 'folder'),array(2, 1, 'file'),array(1, 3, 'file')), array($api, "mv"));

?>
--EXPECTF--
move {!}/D230604.txt {!}/sub1
robocopy {!}/sub1/sub2 {!}/sub1/sub2 /e /move
move {!}/D230604.txt {!}/sub1
robocopy {!}/sub1/sub2 {!}/sub1/sub2 /e /move
move {!}/D230604.txt {!}/sub1
robocopy {!}/sub1/sub2 {!}/sub1/sub2 /e /move
Move ({!}/D230604.txt | {!}/sub1)
Move ({!}/sub1/sub2 | {!}/sub1)
Move ({!}/D230604.txt | {!}/sub1)
Move ({!}/sub1/sub2 | {!}/sub1)
Move ({!}/D230604.txt | {!}/sub1)
Move ({!}/sub1/sub2 | {!}/sub1)
Move ({!}/D230604.txt | {!}/sub1)
Move ({!}/sub1/sub2 | {!}/sub1)
Move ({!}/D230604.txt | {!}/sub1)
Move ({!}/sub1/sub2 | {!}/sub1)
Move ({!}/D230604.txt | {!}/sub1)
Move ({!}/sub1/sub2 | {!}/sub1)
Move 3 to 1 from folders
Move 2 to 1 from files
Move 1 to 3 from files
