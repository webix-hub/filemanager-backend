--TEST--
Basic operations
--FILE--
<?php

include_once dirname(__FILE__) . "/../files_api.php";
$sandbox = realpath(__DIR__."/sandbox");

$api = new RealFileSystem($sandbox);
echo json_encode($api->ls("sub1", true))."\n";

?>
--EXPECTF--
[{"id":"sub1\/Makefile","value":"Makefile","type":"","size":562,"date":1403766433},{"id":"sub1\/sub2","value":"sub2","type":"folder","size":0,"date":1418285046,"data":[{"id":"sub1\/sub2\/LICENSE","value":"LICENSE","type":"","size":1048,"date":1418285037},{"id":"sub1\/sub2\/README.md","value":"README.md","type":"text","size":27709,"date":1403765196},{"id":"sub1\/sub2\/zalgo.js","value":"zalgo.js","type":"code","size":52,"date":1403765196}]}]
