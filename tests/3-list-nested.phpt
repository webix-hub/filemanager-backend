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
D230604.txt, text, 6, D230604.txt
D231019.txt, text, 6, D231019.txt
D231440.txt, text, 6, D231440.txt
D234028.txt, text, 6, D234028.txt
D236850.txt, text, 6, D236850.txt
D242865.txt, text, 6, D242865.txt
D260541.txt, text, 6, D260541.txt
E223016.txt, text, 6, E223016.txt
sub1, folder, 0, sub1
- Makefile, , 562, sub1/Makefile
- sub2, folder, 0, sub1/sub2
- - LICENSE, , 1048, sub1/sub2/LICENSE
- - README.md, text, 27709, sub1/sub2/README.md
- - zalgo.js, code, 52, sub1/sub2/zalgo.js
