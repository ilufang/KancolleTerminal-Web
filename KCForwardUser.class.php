<?php
/**
 *	KCForwardUser
 *
 *	A forwarding user (gamemode=2)
 *	MySQL Wrapper
 *
 *	2015 by ilufang
 */

require_once "KCUser.class.php";

class KCForwardUser {

public $id, $server, $dmmid, $token, $lastupdate, $starttime;
public $kcaccess, $respacks;
public $init_status = false;

/**
 *	init
 *
 *	Initialize object with given info
 *	Set object state as ready
 */
function init($info) {
	$this->id = $info["memberid"];
	$this->server = $info["serveraddr"];
	$this->dmmid = $info["dmmid"];
	$this->lastupdate = $info["lastupdate"];
	$this->token = $info["token"];
	$this->starttime = $info["starttime"];
	$this->kcaccess = json_decode($info["kcaccess"],true);
	$this->respacks = explode("\n", $info["respacks"]);

	$this->init_status = true;
}

/**
 *	initWithToken
 *
 *	Initialize by searching the database with a specific token
 */
function initWithUserId($memberid) {
	$userinfo = KCSql::inst()->selectAll("forward_users")->where("memberid=$memberid")->query();
	if (!is_array($userinfo)) {
		return false;
	}
	$this->init($userinfo[0]);
}


/**
 *	Constructor - Manual
 *
 *	Builds the user according to a token
 */
function __construct($user) {
	$this->initWithUserId($user->id);
}

}
