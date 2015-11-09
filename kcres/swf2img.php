<?php
function delTree($dir) {
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

function fetchShipImg($idx) {
	$db = json_decode(file_get_contents("../kcapi/gamedb.json"), true);
	$filename = $db["ships"][$idx]["api_filename"];
	if (!$filename) {
		return false;
	}
	delTree("images/ship/$idx");
	require_once 'KCResource.class.php';
	$res = new KCResource("/kcs/resources/swf/ships/$filename.swf");
	$res->download();
	mkdir("images/ship/$idx");
	$cmd = "/usr/local/ffdec/ffdec.jar -format image:png -export image images/ship/$idx files/kcs/resources/swf/ships/$filename.swf";
	file_put_contents("images/ship/$idx/export.log", "Extracting swf for ship ".$db["ships"][$idx]["api_id"]." ".$db["ships"][$idx]["api_name"]." (file:$filename)...\nExecuting jar: $cmd\n");
	system("java -jar $cmd >> images/ship/$idx/export.log");
	return true;
}
