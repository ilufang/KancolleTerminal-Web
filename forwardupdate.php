<?php
/**
 *	forwardupdate
 *
 *	Update user's forward settings
 *	Ajax responder
 *
 *	2015 by ilufang
 */

header("Content-Type: application/json");
require_once 'KCUser.class.php';
$user = new KCUser();
if (!$user->initWithAuth($_REQUEST["username"],$_REQUEST["passhash"])) {
	die(json_encode(array("success"=>false, "reason"=>"用户验证失败")));
}
if ($user->gamemode!=3) {
	die(json_encode(array("success"=>false, "reason"=>"gamemode不匹配:当前为{$user->gamemode},必须为3")));
}
require_once 'KCSql.class.php';
if (intval($_REQUEST["dmmid"])>0) {
	if(!KCSql::inst()->update(array("dmmid"=>intval($_REQUEST["dmmid"])),"forward_users")->where("memberid={$user->id}")->query()) {
		die(json_encode(array("success"=>false, "reason"=>"数据库错误:".KCSql::inst()->error())));
	}
}

if (strlen($_REQUEST["swfurl"])>0) {
	$server = "";
	$token = "";
	$starttime = 0;

//	sscanf($_REQUEST["swfurl"], "http://%s/kcs/mainD2.swf?api_token=%s&api_starttime=%d", $server, $token, $starttime);

	$parts = explode("/", substr($_REQUEST["swfurl"], strlen("http://")));
	
	if ($parts[1]!=="kcs") {
		die(json_encode(array("success"=>false, "reason"=>"无效的URL (Directory mismatch):$parts[1]")));
	}
	$server = $parts[0];

	$parts = explode("?", $parts[2]);

	if (strcasecmp($parts[0], "mainD2.swf")!=0) {
		die(json_encode(array("success"=>false, "reason"=>"无效的URL (Filename mismatch):$parts[0]")));
	}

	$parts = explode("&", $parts[1]);

	foreach ($parts as $arg) {
		$comp = explode("=", $arg);
		switch ($comp[0]) {
			case "api_token":
				$token = $comp[1];
				break;
			case "api_starttime":
				$starttime = intval($comp[1]);
				break;
		}
	}


	if (strlen($server)>0 && strlen($token)>0 && $starttime>0) {
		if(!KCSql::inst()->update(array("serveraddr"=>$server, "token"=>$token, "starttime"=>$starttime, "lastupdate"=>date("M d Y h:i:s A")),"forward_users")->where("memberid={$user->id}")->query()) {
			die(json_encode(array("success"=>false, "reason"=>"数据库错误:".KCSql::inst()->error())));
		}
		if(!KCSql::inst()->update(array("token"=>$token),"hub_users")->where("memberid={$user->id}")->query()) {
			die(json_encode(array("success"=>false, "reason"=>"数据库错误:".KCSql::inst()->error())));
		}
	} else {
		die(json_encode(array("success"=>false, "reason"=>"无效的URL (Data incomplete)")));
	}
}

die(json_encode(array("success"=>true)));

