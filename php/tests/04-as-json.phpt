--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../CommandFileSystem.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new CommandFileSystem($sandbox);
echo json_encode($api->ls("sub1", true))."\n";

$api->virtualRoot("Files");
echo json_encode($api->ls("sub1", true))."\n";

?>
--EXPECTF--
[{"id":"sub1\/sub2","value":"sub2","type":"folder","size":0,"date":1418827573,"data":[{"id":"sub1\/sub2\/LICENSE","value":"LICENSE","type":"","size":1067,"date":1418827573},{"id":"sub1\/sub2\/README.md","value":"README.md","type":"text","size":28368,"date":1418827573},{"id":"sub1\/sub2\/zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1418827573}]},{"id":"sub1\/Makefile","value":"Makefile","type":"","size":562,"date":1403766433}]
[{"value":"Files","type":"folder","size":0,"date":0,"id":"\/","data":[{"id":"sub1\/sub2","value":"sub2","type":"folder","size":0,"date":1418827573,"data":[{"id":"sub1\/sub2\/LICENSE","value":"LICENSE","type":"","size":1067,"date":1418827573},{"id":"sub1\/sub2\/README.md","value":"README.md","type":"text","size":28368,"date":1418827573},{"id":"sub1\/sub2\/zalgo.js","value":"zalgo.js","type":"code","size":53,"date":1418827573}]},{"id":"sub1\/Makefile","value":"Makefile","type":"","size":562,"date":1403766433}],"open":true}]
