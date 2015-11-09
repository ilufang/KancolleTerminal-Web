<?php
header("Content-Type: application/json;charset=utf-8");
require_once '../KCSql.class.php';
if (!isset($_REQUEST['user'])||!$_REQUEST['user']) {
	header("HTTP/1.0 400 Bad Request");
	die(json_encode(array('status'=>400)));
}
$user = $_REQUEST['user'];
$records = KCSql::inst()->selectAll("game_data")->where("userid=$user")->query();
if (!$records) {
	header("HTTP/1.0 500 Internal Server Error");
	die(json_encode(array('status'=>500)));
}
$gamerec = json_decode($records[0]["gamedata"], true);
$port = array(
	'api_basic'=>$gamerec['api_basic'],
	'api_ndock'=>$gamerec['api_ndock'],
	'api_kdock'=>$gamerec['api_kdock'],
	'api_material'=>$gamerec['api_material'],
	'api_deck_port'=>$gamerec['api_deck'],
	'api_combined_flag'=>$gamerec['api_combined_flag'],
	'api_ship'=>$gamerec['api_ship'],
	'api_slot_item'=>$gamerec['api_slot_item']
);

if ($_REQUEST['action']==='start2') {
	$start2 = json_decode(file_get_contents("../kcapi/start2.json"), true);
	if (!$start2) {
		header("HTTP/1.0 404 Not Found");
		die(json_encode(array('status'=>404)));
	}
	die(json_encode(array('status'=>200,'detail'=>array(
	    'path' => '/kcsapi/api_start2',
	    'body' => $start2
	))));
} elseif ($_REQUEST['action']==='port') {
	die(json_encode(array('status'=>200,"detail"=>array(
	    'path' => '/kcsapi/api_port/port',
	    'body' => $port
	))));
} else {
	$battleScene = $gamerec['battle'];
	if (isset($_REQUEST['checksum']) && $_REQUEST['checksum'] === $battleScene['checksum']) {
		die(json_encode(array('status'=>304)));
	}

	if ($battleScene['path']==='/kcsapi/api_port/port') {
		die(json_encode(array('status'=>200,"detail"=>array(
		    'path' => '/kcsapi/api_port/port',
		    'checksum' => $checksum,
		    'body' => $port
		))));
	} else {
		die(json_encode($battleScene));
	}
}
?>
