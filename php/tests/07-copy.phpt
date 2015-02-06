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
$api->cp("D230604.txt", "sub1\\sub2");
$api->cp("sub1\\sub2", "");

$api = new PHPFileSystem($sandbox);
$api->test = true;
$api->cp("D230604.txt", "sub1\\sub2");
$api->cp("sub1\\sub2", "");

$filesystem = new Filesystem(new Adapter($sandbox));
$api = new LocalFlyFileSystem($filesystem, $sandbox);
$api->test = true;
$api->cp("D230604.txt", "sub1\\sub2");
$api->cp("sub1\\sub2", "");

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
$api->cp(3, 1, 'folder');
$api->cp(2, 1, 'file');
?>
--EXPECTF--
copy {!}/D230604.txt {!}/sub1/sub2
robocopy {!}/sub1/sub2 {!}/sub2 /e
Copy ({!}/D230604.txt | {!}/sub1/sub2)
Copy ({!}/sub1/sub2 | {!}/)
Copy ({!}/D230604.txt | {!}/sub1/sub2)
Copy ({!}/sub1/sub2 | {!}/)
Copy 3 to 1 from folders
Copy 2 to 1 from files
