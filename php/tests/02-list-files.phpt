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
$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
$api->test = true;

$data = $api->ls("sub1/sub2");
listlog($data, "");

$data = $api->ls("sub1");
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
