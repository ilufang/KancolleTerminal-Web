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
$port = $gamerec["api_port"];

if (!isset($_REQUEST['action'])) {
	// Prevent warnings spamming the log
	$_REQUEST['action'] = '';
}

if ($_REQUEST['action']==='start2') {
	$start2 = json_decode(file_get_contents("../kcapi/start2.json"), true);
	if (!$start2) {
		header("HTTP/1.0 404 Not Found");
		die(json_encode(array('status'=>404)));
	}
	die(json_encode(array('status'=>200,'detail'=>array(
	    'path' => '/kcsapi/api_start2',
	    'body' => $start2,
	    'postBody' => array(),
	    'method' => 'POST'
	))));
} elseif($_REQUEST['action']==='init') {
	// Initialization sequence
	die(json_encode(array('status'=>200, 'seq'=> array(
		array(
			'path' => '/kcsapi/api_get_member/useitem',
			'method' => 'POST',
			'body' => $gamerec["api_useitem"],
			'postBody' => array()
		),
		array(
			'path' => '/kcsapi/api_get_member/kdock',
			'method' => 'POST',
			'body' => $gamerec["api_kdock"],
			'postBody' => array()
		),
		array(
			'path' => '/kcsapi/api_get_member/basic',
			'method' => 'POST',
			'body' => $gamerec["api_basic"],
			'postBody' => array()
		),
		array(
			'path' => '/kcsapi/api_get_member/slot_item',
			'method' => 'POST',
			'body' => $gamerec["api_slot_item"],
			'postBody' => array()
		)
	))));
} elseif ($_REQUEST['action']==='port') {
	die(json_encode(array('status'=>200,"detail"=>array(
	    'path' => '/kcsapi/api_port/port',
	    'body' => $port,
	    'postBody' => array(),
	    'method' => 'POST'
	))));
} elseif ($_REQUEST['action']==='quest'){
	require_once '../kcapi/KCRequest.class.php';
	$questlist = array();
	$user_kc = new KCUser();
	if (!$user_kc->initWithID($user)) {
		header("HTTP/1.1 401 Unauthorized");
		die(json_encode(array('status' => 401)));
	}

	$pagecount = 10; // Dynamic update
	// Just other data
	$exec_count = 0;
	$exec_type = 0;
	$questcount = 0;

	// Iterate scope control
	$beginpage = 0;
	$endpage = 10;
	if (isset($_REQUEST["quest_page"])) {
		// Indexes begin with 0
		$beginpage = intval($_REQUEST["quest_page"])-1;
		$endpage = intval($_REQUEST["quest_page"])-1;
	}

	// Iterate thru pages
	for ($i=$beginpage; ($i<$pagecount && $i<=$endpage); $i++) {
		$quest = new KCRequest("/kcsapi/api_get_member/questlist", array("api_token"=>$user_kc->token, "api_verno"=>1, "api_page_no"=>($i+1)), getallheaders());
		$result = $quest->printResponse();
		$result = json_decode(substr($result, strlen("svdata=")), true);
		if ($result["api_result"]==1) {
			$pagecount = $result["api_data"]["api_page_count"];
			$questcount = $result["api_data"]["api_count"];
			$exec_count = $result["api_data"]["api_exec_count"];
			$exec_type = $result["api_data"]["api_exec_type"];
			$questlist = array_merge($questlist, $result["api_data"]["api_list"]);
		} else {
			// Error happened, report that.
			header("HTTP/1.1 502 Bad Gateway");
			//var_dump($quest);
			die(json_encode(array('status' => 502)));
		}
	}
	die(json_encode(array('status' => 200, 'detail'=>array(
	    'path' => '/kcsapi/api_get_member/questlist',
	    'body' => array(
			"api_count" => $questcount,
			"api_page_count" => $pagecount,
			"api_disp_page" => $i,
			"api_list" => $questlist,
			"api_exec_count" => $exec_count,
			"api_exec_type" => $exec_type
		),
	    'postBody' => array(), // No postbody is required for this request
	    'method' => 'POST'
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
		    'body' => $port,
		    'postBody' => array()
		))));
	} else {
		die(json_encode($battleScene));
	}
}
?>
