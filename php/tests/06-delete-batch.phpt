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
$api->batch(array(array(1, "folder"),array(1, "file"),array(3, "folder")), array($api, "rm"));

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
Delete 1 from folders
Delete 1 from files
Delete 3 from folders
