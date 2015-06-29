<?php
/**
 *	oscap
 *
 *	Screen makeRequest traffic to osapi.dmm.com
 *	Capture user's server and token
 *
 *	2015 by ilufang
 */

$serverip="";
$dmmid="";
$token="";
$starttime="";

require_once "KCSql.class.php";


/**
 *	writeUserToken
 *
 *	Saves previously parsed data to the mysql database
 */
function writeUserToken() {
	global $serverip, $dmmid, $token, $starttime;
	$userinfo = KCSql::inst()->select(array("memberid"),"forward_users")->where("dmmid=$dmmid")->query();
	if (is_array($userinfo)) {
		$userid = $userinfo[0]["memberid"];
		if(KCSql::inst()->update(array("token"=>$token,"starttime"=>$starttime,"serveraddr"=>$serverip,"lastupdate"=>date("M d Y h:i:s A")),"forward_users")->where("memberid=$userid")->query()!==false){
			// Update user as well
			KCSql::inst()->update(array("token"=>$token),"hub_users")->where("memberid=$userid")->query();
		} else {
			file_put_contents("write.txt", "req_err:".KCSql::inst()->error());
		}
	} else {
		file_put_contents("write.txt", "array0:".KCSql::inst()->error());
	}
}

/**
 *	processURL
 *
 *	Parse the url-portion of the response
 *	Saves to global variable if found
 *
 *	@return Boolean - Whether the url is a valid one
 */
function processURL($url) {
	global $serverip, $dmmid;
	$prefix = "http://";
	$parts = explode("/", substr($url, strlen($prefix)));
	if (count($parts)!=7) {
		return false;
	}
	if ($parts[1]!=="kcsapi") {
		return false;
	}
	if ($parts[2]!=="api_auth_member") {
		return false;
	}
	if ($parts[3]!=="dmmlogin") {
		return false;
	}
	$serverip = $parts[0];
	$dmmid = $parts[4];
	return true;
}

/**
 *	processResult
 *
 *	Parse the body-portion of the response
 *	Saves to global variable if found, and call writeUserToken to write all data into db
 */
function processResult($result) {
	global $token, $starttime;
	$body = $result["body"];
	// Parse "svdata" json
	$prefix = "svdata=";
	$data = json_decode(substr($body, strlen($prefix)),true);
	if ($data["api_result"]==1) {
		$token = $data["api_token"];
		$starttime = $data["api_starttime"];
		writeUserToken();
	}
}

/**
 *	screenRequest
 *
 *	Processes a request from OpenSocial Proxy
 *	The process must be a makeRequest request
 */
function screenRequest($body) {
	$prefix = "throw 1; < don't be evil' >";
	$data = json_decode(substr($body, strlen($prefix)),true);
	if (!is_array($data)) {
		file_put_contents("jsonerr.json", substr($body, strlen($prefix)));
		return;
	}
	foreach ($data as $url => $result) {
		if (processURL($url)) {
			processResult($result);
		}
	}
}

/*
throw 1; < don't be evil' >
{"http://125.6.188.25/kcsapi/api_auth_member/dmmlogin/20186645/1/1435398513868":
{"rc":200,"body":"svdata={"api_result":1,"api_result_msg":"成功","api_token":"1a0d6c232d3aab98bac95acaf8526f42b3b5b85e","api_starttime":1435398514371}","headers":{"Server":"Apache","X-Powered-By":"PHP/5.3.3","Connection":"close","Content-Type":"text/plain"}}}

svdata={"api_result":1,"api_result_msg":"成功","api_token":"1a0d6c232d3aab98bac95acaf8526f42b3b5b85e","api_starttime":1435398514371}

*/