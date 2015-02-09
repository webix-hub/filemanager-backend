--TEST--
Basic operations
--FILE--
<?php

function echojson($obj){
	echo preg_replace("|[0-9]{10}|", "1234567890", json_encode($obj))."\n";
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
echojson( $api->ls("sub1", true) );

$api->virtualRoot("Files");
echojson( $api->ls("sub1", true) );

$api = new PHPFileSystem($sandbox);
echojson( $api->ls("sub1", true) );

$api->virtualRoot("Files");
echojson( $api->ls("sub1", true) );

$filesystem = new Filesystem(new Adapter($sandbox));
$api = new LocalFlyFileSystem($filesystem, $sandbox);
echojson( $api->ls("sub1", true) );

$api->virtualRoot("Files");
echojson( $api->ls("sub1", true) );

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
echojson( $api->ls(1, true) );

$api->virtualRoot(1);
echojson( $api->ls(1, true) );

?>
--EXPECTF--
[{"id":"sub1\/sub2","value":"sub2","type":"folder","size":0,"date":1234567890,"data":[{"id":"sub1\/sub2\/LICENSE","value":"LICENSE","type":"","size":1067,"date":1234567890},{"id":"sub1\/sub2\/README.md","value":"README.md","type":"text","size":28368,"date":1234567890},{"id":"sub1\/sub2\/zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1234567890}]},{"id":"sub1\/Makefile","value":"Makefile","type":"","size":532,"date":1234567890}]
[{"value":"Files","type":"folder","size":0,"date":0,"id":"\/","data":[{"id":"sub1\/sub2","value":"sub2","type":"folder","size":0,"date":1234567890,"data":[{"id":"sub1\/sub2\/LICENSE","value":"LICENSE","type":"","size":1067,"date":1234567890},{"id":"sub1\/sub2\/README.md","value":"README.md","type":"text","size":28368,"date":1234567890},{"id":"sub1\/sub2\/zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1234567890}]},{"id":"sub1\/Makefile","value":"Makefile","type":"","size":532,"date":1234567890}],"open":true}]
[{"id":"sub1\/sub2","value":"sub2","type":"folder","size":0,"date":1234567890,"data":[{"id":"sub1\/sub2\/LICENSE","value":"LICENSE","type":"","size":1067,"date":1234567890},{"id":"sub1\/sub2\/README.md","value":"README.md","type":"text","size":28368,"date":1234567890},{"id":"sub1\/sub2\/zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1234567890}]},{"id":"sub1\/Makefile","value":"Makefile","type":"","size":532,"date":1234567890}]
[{"value":"Files","type":"folder","size":0,"date":0,"id":"\/","data":[{"id":"sub1\/sub2","value":"sub2","type":"folder","size":0,"date":1234567890,"data":[{"id":"sub1\/sub2\/LICENSE","value":"LICENSE","type":"","size":1067,"date":1234567890},{"id":"sub1\/sub2\/README.md","value":"README.md","type":"text","size":28368,"date":1234567890},{"id":"sub1\/sub2\/zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1234567890}]},{"id":"sub1\/Makefile","value":"Makefile","type":"","size":532,"date":1234567890}],"open":true}]
[{"id":"sub1\\sub2","value":"sub2","type":"folder","size":0,"date":1234567890,"data":[{"id":"sub1\\sub2\\LICENSE","value":"LICENSE","type":"","size":1067,"date":1234567890},{"id":"sub1\\sub2\\README.md","value":"README.md","type":"text","size":28368,"date":1234567890},{"id":"sub1\\sub2\\zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1234567890}]},{"id":"sub1\\Makefile","value":"Makefile","type":"","size":532,"date":1234567890}]
[{"value":"Files","type":"folder","size":0,"date":0,"id":"\/","data":[{"id":"sub1\\sub2","value":"sub2","type":"folder","size":0,"date":1234567890,"data":[{"id":"sub1\\sub2\\LICENSE","value":"LICENSE","type":"","size":1067,"date":1234567890},{"id":"sub1\\sub2\\README.md","value":"README.md","type":"text","size":28368,"date":1234567890},{"id":"sub1\\sub2\\zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1234567890}]},{"id":"sub1\\Makefile","value":"Makefile","type":"","size":532,"date":1234567890}],"open":true}]
[{"id":"3","value":"st","type":"folder","data":[{"id":"4","value":"nono","type":"folder","data":[{"id":"2","value":"br.json","type":"code","color":"black","size":"15500"}]}]},{"id":"2","value":"st2","type":"folder","data":[]},{"id":"1","value":"text.txt","type":"text","color":"red","size":"10000"}]
[{"value":"root","type":"folder","id":"1","data":[{"id":"3","value":"st","type":"folder","data":[{"id":"4","value":"nono","type":"folder","data":[{"id":"2","value":"br.json","type":"code","color":"black","size":"15500"}]}]},{"id":"2","value":"st2","type":"folder","data":[]},{"id":"1","value":"text.txt","type":"text","color":"red","size":"10000"}],"open":true}]
