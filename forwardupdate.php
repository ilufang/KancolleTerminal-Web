<?php
/**
 *	forwardupdate
 *
 *	Update user's forward settings (login and set game url)
 *	Ajax responder
 *
 *	2015 by ilufang
 */

header("Content-Type: application/json");
require_once 'KCUser.class.php';
$user = new KCUser();
if (!$user->initWithToken($_REQUEST["token"])) {
	die(json_encode(array("success"=>false, "reason"=>"用户验证失败")));
}
if ($user->gamemode!=3) {
	die(json_encode(array("success"=>false, "reason"=>"gamemode不匹配:当前为{$user->gamemode},必须为3")));
}
require_once 'KCSql.class.php';

if (strlen($_REQUEST['dmmuser'])>0 && strlen($_REQUEST['dmmpass'])>0) {
	require_once 'dmmauth/dmmLogin.class.php';
	$creds = dmmLogin::login($_REQUEST['dmmuser'], $_REQUEST['dmmpass']);
	if (!$creds) {
		die(json_encode(array("success"=>false, "reason"=>"登录脚本发生错误")));
	}
	if (strcasecmp($creds['status'],"success")==0) {
		$server = $creds['serverip'];
		$token = $creds['token'];
		$starttime = intval($creds['starttime']);
		$dmmid = intval($creds['owner']);
		if(!KCSql::inst()->update(array("serveraddr"=>$server, "token"=>$token, "starttime"=>$starttime, "dmmid"=>$dmmid, "lastupdate"=>"NOW()"),"forward_users")->where("memberid={$user->id}")->query()) {
			die(json_encode(array("success"=>false, "reason"=>"数据库错误:".KCSql::inst()->error())));
		}
		if(!KCSql::inst()->update(array("token"=>$token),"hub_users")->where("memberid={$user->id}")->query()) {
			die(json_encode(array("success"=>false, "reason"=>"数据库错误:".KCSql::inst()->error())));
		}
		die(json_encode(array("success"=>true)));
	} else {
		die(json_encode(array("success"=>false, "reason"=>"登录脚本发生错误: ".$creds['status'])));
	}
}

if (intval($_REQUEST["dmmid"])>0) {
	if(!KCSql::inst()->update(array("dmmid"=>intval($_REQUEST["dmmid"])),"forward_users")->where("memberid={$user->id}")->query()) {
		die(json_encode(array("success"=>false, "reason"=>"数据库错误:".KCSql::inst()->error())));
	}
}

if (isset($_REQUEST["swfurl"]) && strlen($_REQUEST["swfurl"])>0) {
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
		if(!KCSql::inst()->update(array("serveraddr"=>$server, "token"=>$token, "starttime"=>$starttime, "lastupdate"=>"NOW()"),"forward_users")->where("memberid={$user->id}")->query()) {
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

