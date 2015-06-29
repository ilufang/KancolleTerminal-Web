<?php
/**
 *	KCUser
 *
 *	A certain user
 *	MySQL Wrapper
 *
 *	2015 by ilufang
 */

require_once "KCSql.class.php";

class KCUser {


/**
 *	gamemode
 *
 *	The user gamemode. Represented as Minecraft gamemode
 *
 *	0 - Survival:	Regular Internal Engine
 *	1 - Creative:	Customize everything, even cheats
 *	2 - Adventure:	Realistic Engine, treats enemy and allied equally, frequent sinks
 *	3 - Spectator:	Forward everything to Kadokawa servers
 */
public $id, $name, $gamemode, $token;
public $init_status = false;

/**
 *	init
 *
 *	Initialize object with given info
 *	Set object state as ready
 */
function init($info) {
	$this->id = $info["memberid"];
	$this->name = $info["username"];
	$this->gamemode = $info["gamemode"];
	$this->token = $info["token"];

	$this->init_status = true;
}

/**
 *	initWithToken
 *
 *	Initialize by searching the database with a specific token
 */
function initWithToken($token) {
	$this->token = $token;
	$userinfo = KCSql::inst()->selectAll("hub_users")->where("token='$token'")->query();
	if (!is_array($userinfo)) {
		return false;
	}
	$this->init($userinfo[0]);
	return true;
}

/**
 * initWithSession
 *
 * Builds the user according to the current browser session (Determine by Referer)
 */
function initWithSession() {
	$ref = $_SERVER["HTTP_REFERER"];
	$desc = "api_token=";
	$idx = strpos($ref, $desc)+strlen($desc);
	$token = substr($ref, $idx, 40); // token length
	return $this->initWithToken($token);
}


/**
 *	initWithAuth
 *
 *	Builds the user with the given username if password authentication succeeded
 */
function initWithAuth($username, $password) {
	$userinfo = KCSql::inst()->selectAll("hub_users")->where("username='$username'")->query();
	if (is_array($userinfo)) {
		if (sha1($password) === $userinfo[0]["password"]) {
			$this->init($userinfo[0]);
			return true;
		} else {
			return false;
		}
	}
	return false;
}


}
