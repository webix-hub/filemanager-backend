--TEST--
Basic operations
--FILE--
<?php

if (!function_exists("listlog")){
	function listlog($data, $level){
		foreach ($data as $key => $value){
			echo $level.$value["value"].", ".$value["type"].", ".$value["id"]."\n";
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

$data = $api->ls("/", true);
listlog($data,"");

$api = new PHPFileSystem($sandbox);
$api->test = true;

$data = $api->ls("/", true);
listlog($data,"");

$filesystem = new Filesystem(new Adapter($sandbox));
$api = new LocalFlyFileSystem($filesystem, $sandbox);
$api->test = true;

$data = $api->ls("/", true);
listlog($data,"");


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
        'folder_id' => 'folder_id',
        'data_fields' => 'color, size'
    )
);

$api = new DBFileSystem($config, $config_folders, $config_files);
$api->test = true;
$data = $api->ls(1, true);
listlog($data,"");
?>
--EXPECTF--
List {!}/
List {!}/sub1/
List {!}/sub1/sub2/
sub1, folder, sub1
- sub2, folder, sub1/sub2
- - LICENSE, , sub1/sub2/LICENSE
- - README.md, text, sub1/sub2/README.md
- - zalgo.js, code, sub1/sub2/zalgo.js
- Makefile, , sub1/Makefile
D230604.txt, text, D230604.txt
D231019.txt, text, D231019.txt
D231440.txt, text, D231440.txt
D234028.txt, text, D234028.txt
D236850.txt, text, D236850.txt
D242865.txt, text, D242865.txt
D260541.txt, text, D260541.txt
E223016.txt, text, E223016.txt
List {!}/
List {!}/sub1/
List {!}/sub1/sub2/
sub1, folder, sub1
- sub2, folder, sub1/sub2
- - LICENSE, , sub1/sub2/LICENSE
- - README.md, text, sub1/sub2/README.md
- - zalgo.js, code, sub1/sub2/zalgo.js
- Makefile, , sub1/Makefile
D230604.txt, text, D230604.txt
D231019.txt, text, D231019.txt
D231440.txt, text, D231440.txt
D234028.txt, text, D234028.txt
D236850.txt, text, D236850.txt
D242865.txt, text, D242865.txt
D260541.txt, text, D260541.txt
E223016.txt, text, E223016.txt
List {!}/
List {!}/sub1/
List {!}/sub1/sub2/
sub1, folder, sub1
- sub2, folder, sub1\sub2
- - LICENSE, , sub1\sub2\LICENSE
- - README.md, text, sub1\sub2\README.md
- - zalgo.js, code, sub1\sub2\zalgo.js
- Makefile, , sub1\Makefile
D230604.txt, text, D230604.txt
D231019.txt, text, D231019.txt
D231440.txt, text, D231440.txt
D234028.txt, text, D234028.txt
D236850.txt, text, D236850.txt
D242865.txt, text, D242865.txt
D260541.txt, text, D260541.txt
E223016.txt, text, E223016.txt
List from 1
List from 2
List from 3
List from 4
st, folder, 3
- nono, folder, 4
- - br.json, code, 2
st2, folder, 2
text.txt, text, 1
