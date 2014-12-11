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

include_once dirname(__FILE__) . "/../files_api.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new RealFileSystem($sandbox);
$api->debug = true;

$data = $api->ls("/", true);
listlog($data,"");

?>
--EXPECTF--
List C:\http\php-files-api\tests\sandbox\
List C:\http\php-files-api\tests\sandbox\sub1\
List C:\http\php-files-api\tests\sandbox\sub1\sub2\
D230604.txt, file, 6, D230604.txt
D231019.txt, file, 6, D231019.txt
D231440.txt, file, 6, D231440.txt
D234028.txt, file, 6, D234028.txt
D236850.txt, file, 6, D236850.txt
D242865.txt, file, 6, D242865.txt
D260541.txt, file, 6, D260541.txt
E223016.txt, file, 6, E223016.txt
sub1, dir, 0, sub1
- Makefile, file, 562, sub1/Makefile
- sub2, dir, 0, sub1/sub2
- - LICENSE, file, 1048, sub1/sub2/LICENSE
- - README.md, file, 27709, sub1/sub2/README.md
- - zalgo.js, file, 52, sub1/sub2/zalgo.js
