<?php
header("Content-Type: text/plain; charset=utf-8");
require_once 'swf2img.php';

if (isset($_REQUEST['id'])) {
	$idx = $_REQUEST['id'];
	if (fetchShipImg($idx)){
		header("Location: shipimg.php?id=$_REQUEST[id]");
		die();
	} else {
		die("An error occurred.");
	}
}

if (!isset($argv)) {
	die("Batch update is for CLI execution only");
}

if (ob_get_level() == 0) ob_start();
ob_flush();
flush();

$db = json_decode(file_get_contents("../kcapi/gamedb.json"), true);

foreach ($db["ships"] as $ship) {
	if (file_exists("images/ship/$ship[api_id]")) {
		echo "Skipping $ship[api_name]...\n";
	} else {
		echo "Fetching $ship[api_name]...\n";
		ob_flush();
		flush();
		fetchShipImg($ship["api_id"]);
	}
}

ob_end_flush();


