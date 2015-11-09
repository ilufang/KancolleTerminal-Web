<?php

require_once '../kcres/KCResource.class.php';

error_reporting(E_ERROR | E_PARSE);

function addZeros($num) {
	$num++;
	if ($num<10) {
		return "00$num";
	} else if ($num<100) {
		return "0$num";
	} else {
		return "$num";
	}
}

$typeurl = array("floor","wall","window","object","chest","desk");
$db = json_decode(file_get_contents("../kcapi/gamedb.json"),true);
$badfurns = [];

echo "Resource iteration begin. \n";
echo "Loaded furnitures: ".count($db['furniture'])."\n";

foreach ($db["furniture"] as $furniture) {
	$url = "/kcs/resources/image/furniture/".$typeurl[$furniture["api_type"]]."/".addZeros($furniture["api_no"]);
	$res_swf = new KCResource("$url.swf", FALSE);
	$res_png = new KCResource("$url.png", FALSE);
	echo "$furniture[api_title]...";
	if (!$res_png->init() && !$res_swf->init()) {
		$badfurns[] = $furniture["api_id"];
		echo "FAIL\n";
	} else {
		echo "OK\n";
	}
}

echo json_encode($badfurns)."\n";

file_put_contents("../kcapi/badfurns.json", json_encode($badfurns));
