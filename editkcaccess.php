<?php
/**
 *	editkcaccess
 *
 *	Update user's kcaccess translator settings
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

if(!KCSql::inst()->update(array("kcaccess"=>utf8_encode($_REQUEST["kcaccess"])),"forward_users")->where("memberid={$user->id}")->query()) {
	die(json_encode(array("success"=>false, "reason"=>"数据库错误:".KCSql::inst()->error())));
}

die(json_encode(array("success"=>true)));

//echo $_REQUEST["kcaccess"];
