--TEST--
Basic operations
--FILE--
<?php

function echojson($obj){
	echo preg_replace("|[0-9]{10}|", "1234567890", json_encode($obj))."\n";
}

include_once dirname(__FILE__) . "/../CommandFileSystem.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
echojson( $api->ls("sub1", true) );

$api->virtualRoot("Files");
echojson( $api->ls("sub1", true) );


?>
--EXPECTF--
[{"id":"sub1\/sub2","value":"sub2","type":"folder","size":0,"date":1234567890,"data":[{"id":"sub1\/sub2\/LICENSE","value":"LICENSE","type":"","size":1067,"date":1234567890},{"id":"sub1\/sub2\/README.md","value":"README.md","type":"text","size":28368,"date":1234567890},{"id":"sub1\/sub2\/zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1234567890}]},{"id":"sub1\/Makefile","value":"Makefile","type":"","size":532,"date":1234567890}]
[{"value":"Files","type":"folder","size":0,"date":0,"id":"\/","data":[{"id":"sub1\/sub2","value":"sub2","type":"folder","size":0,"date":1234567890,"data":[{"id":"sub1\/sub2\/LICENSE","value":"LICENSE","type":"","size":1067,"date":1234567890},{"id":"sub1\/sub2\/README.md","value":"README.md","type":"text","size":28368,"date":1234567890},{"id":"sub1\/sub2\/zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1234567890}]},{"id":"sub1\/Makefile","value":"Makefile","type":"","size":532,"date":1234567890}],"open":true}]
