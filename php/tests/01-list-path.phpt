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
