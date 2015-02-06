--TEST--
Basic operations
--FILE--
<?php

if (!function_exists("listlog")){
	function listlog($data, $level){
		foreach ($data as $key => $value){
			echo $level.$value["value"].", ".$value["type"].", ".$value["size"].", ".$value["id"]."\n";
			if (isset($value["data"]))
				listlog($value["data"], $level."- ");
		}
	}
}

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

$data = $api->ls("sub1/sub2");
listlog($data, "");

$data = $api->ls("sub1");
listlog($data, "");

$api = new PHPFileSystem($sandbox);
$api->test = true;

$data = $api->ls("sub1/sub2");
listlog($data, "");

$data = $api->ls("sub1");
listlog($data, "");

$filesystem = new Filesystem(new Adapter($sandbox));
$api = new LocalFlyFileSystem($filesystem, $sandbox);
$api->test = true;

$data = $api->ls("sub1/sub2");
listlog($data, "");

$data = $api->ls("sub1");
listlog($data, "");


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
$data = $api->ls(1, false);
listlog($data, "");
?>
--EXPECTF--
List {!}/sub1/sub2/
LICENSE, , 1067, sub1/sub2/LICENSE
README.md, text, 28368, sub1/sub2/README.md
zalgo.js, code, 53, sub1/sub2/zalgo.js
List {!}/sub1/
sub2, folder, 0, sub1/sub2
Makefile, , 532, sub1/Makefile
List {!}/sub1/sub2/
LICENSE, , 1067, sub1/sub2/LICENSE
README.md, text, 28368, sub1/sub2/README.md
zalgo.js, code, 53, sub1/sub2/zalgo.js
List {!}/sub1/
sub2, folder, 0, sub1/sub2
Makefile, , 532, sub1/Makefile
List {!}/sub1/sub2/
LICENSE, , 1067, sub1/sub2\LICENSE
README.md, text, 28368, sub1/sub2\README.md
zalgo.js, code, 53, sub1/sub2\zalgo.js
List {!}/sub1/
sub2, folder, 0, sub1\sub2
Makefile, , 532, sub1\Makefile
List from 1
st, folder, 0, 3
st2, folder, 0, 2
text.txt, text, 0, 1
