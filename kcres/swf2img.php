<?php
/**
 *	Swf 2 Image
 *
 *	Export downloaded ship SWFs to images
 *	WARNING: REQUIRE EXTERNAL SHELL CALLS
 *
 *	2015 by ilufang
 */

/*
Directions:

For this script to work, you need
1. Java 1.8+
2. FFDec (jar format) (https://www.free-decompiler.com/flash/download/)

Then, specify the path to ffdec.jar:
1. Search for TODO in this file (there are 2 occurrences in total)
2. Replace /usr/local/ffdec/ffdec.jar with your path to ffdec.jar

If you do not have permission to install java globally, you also need to specify your java executable path
1. Search for system in this file (there are 2 occurrences in total)
2. Replace java with your path to java executable

If your php does not have shell access at all (eg. on a cpanel), please refer to /ships.php and remove the Image resources column
*/
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
	// TODO: EDIT THIS LINE
	$cmd = "/usr/local/ffdec/ffdec.jar -format image:png -export image images/ship/$idx files/kcs/resources/swf/ships/$filename.swf";
	file_put_contents("images/ship/$idx/export.log", date("h:i:s")."Extracting swf for ship ".$db["ships"][$idx]["api_id"]." ".$db["ships"][$idx]["api_name"]." (file:$filename)...\nExecuting jar: $cmd\n");
	system("java -jar $cmd >> images/ship/$idx/export.log");
	return true;
}

function fetchFilename($filename) {
	if (strlen(trim($filename))==0) {
		return false;
	}
	delTree("images/ship/$filename");
	require_once 'KCResource.class.php';
	$res = new KCResource("/kcs/resources/swf/ships/$filename.swf");
	$res->download();
	mkdir("images/ship/$filename");
	// TODO: EDIT THIS LINE
	$cmd = "/usr/local/ffdec/ffdec.jar -format image:png -export image images/ship/$filename files/kcs/resources/swf/ships/$filename.swf";
	file_put_contents("images/ship/$filename/export.log", date("h:i:s")."Extracting swf for file ".$filename."...\nExecuting jar: $cmd\n");
	system("java -jar $cmd >> images/ship/$filename/export.log");
	return true;
}

if (isset($argv)) {
	if (fetchShipImg(intval($argv[1]))) {
		echo "Done.";
	} else {
		echo "Error.";
	}
}
