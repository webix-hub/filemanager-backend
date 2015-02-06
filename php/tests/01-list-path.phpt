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

spl_autoload_register(function($class) {
    $prefix = 'League\\Flysystem\\';

    if ( ! substr($class, 0, 17) === $prefix) {
        return;
    }

    $class = substr($class, strlen($prefix));
    $location = __DIR__ . '/../flysystem/src/' . str_replace('\\', '/', $class) . '.php';

    if (is_file($location)) {
        require_once($location);
    }
});

$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
$api->test = true;
$api->ls("");
$api->ls("sub1");
$api->ls("sub1/sub2");
$api->ls("sub1/../");
$api->ls("../");

$api = new PHPFileSystem($sandbox);
$api->test = true;
$api->ls("");
$api->ls("sub1");
$api->ls("sub1/sub2");
$api->ls("sub1/../");
$api->ls("../");

$filesystem = new Filesystem(new Adapter($sandbox));
$api = new LocalFlyFileSystem($filesystem, $sandbox);
$api->test = true;
$api->ls("");
$api->ls("sub1");
$api->ls("sub1/sub2");
$api->ls("sub1/../");
$api->ls("../");

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
$api->ls(1, true);
?>
--EXPECTF--
List {!}/
List {!}/sub1/
List {!}/sub1/sub2/
List {!}/sub1/
List {!}/
List {!}/
List {!}/sub1/
List {!}/sub1/sub2/
List {!}/sub1/
List {!}/
List {!}/
List {!}/sub1/
List {!}/sub1/sub2/
List {!}/sub1/
List {!}/
List from 1
List from 2
List from 3
List from 4
