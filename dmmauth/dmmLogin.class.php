<?php
/**
 *	dmmLogin
 *
 *	Acquires flash-relevant information from dmm login credentials
 *
 *	PHP port of kclib, Author: xchange (kancolle.tv)
 *	2015 by ilufang
 */

class dmmLogin {

// Constants
// conf.py
const DMM_LOGIN_URL = 'https://www.dmm.com/my/-/login/';
const AJAX_TOKEN_URL = 'https://www.dmm.com/my/-/login/ajax-get-token/';
const DMM_AUTH_URL = 'https://www.dmm.com/my/-/login/auth/';
const GAME_URL = 'http://www.dmm.com/netgame/social/-/gadgets/=/app_id=854854/';
const WORLD_URL = 'http://203.104.209.7/kcsapi/api_world/get_id/%d/1/%d';
const FLASH_URL = 'http://%s/kcsapi/api_auth_member/dmmlogin/%d/1/%d';
const MAKE_REQUEST_URL = 'http://osapi.dmm.com/gadgets/makeRequest';
static $WORLD_IP = array (
	"203.104.209.71",
	"125.6.184.15",
	"125.6.184.16",
	"125.6.187.205",
	"125.6.187.229",
	"125.6.187.253",
	"125.6.188.25",
	"203.104.248.135",
	"125.6.189.7",
	"125.6.189.39",
	"125.6.189.71",
	"125.6.189.103",
	"125.6.189.135",
	"125.6.189.167",
	"125.6.189.215",
	"125.6.189.247",
	"203.104.209.23",
	"203.104.209.39",
	"203.104.209.55",
	"203.104.209.102"
);

const REQUESTS_TIMEOUT = 30;
const REQUESTS_USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko';
const REQUESTS_COOKIE = "ckcy=1; cklg=welcome";

const DMM_TOKEN_PATTERN = '/"DMM_TOKEN", "([\d|\w]+)"/';
const TOKEN_PATTERN = '/"token": "([\d|\w]+)"/';
const OSAPI_URL_PATTERN = '/URL\W+:\W+"(.*)",/';

private static function request($url, $postdata = NULL, $headers = NULL) {
	set_time_limit(self::REQUESTS_TIMEOUT);
	$s = curl_init();
	curl_setopt($s, CURLOPT_URL, $url);
	curl_setopt($s, CURLOPT_HEADER, TRUE);
	curl_setopt($s, CURLOPT_USERAGENT, self::REQUESTS_USER_AGENT);
	curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($s, CURLOPT_TIMEOUT, self::REQUESTS_TIMEOUT);
	curl_setopt($s, CURLOPT_COOKIE, self::REQUESTS_COOKIE);
	if (isset($headers)) {
		curl_setopt($s, CURLOPT_HTTPHEADER, $headers);
	}
	if (isset($postdata)) {
		$urlencoded = "";
		foreach ($postdata as $key => $value) {
			$urlencoded .= "$key=$value&";
		}
		$urlencoded = substr($urlencoded, 0, strlen($urlencoded)-1);
		$urlencoded = str_replace("@", "%40", $urlencoded);
		echo $urlencoded."\n";
		curl_setopt ($s, CURLOPT_POST, true);
		curl_setopt ($s, CURLOPT_POSTFIELDS, $urlencoded);
	}
	$response = curl_exec($s);
	if (curl_error($s)) {
		throw new Exception("CURL error: ".curl_error($s), 1);
	}
	$response = str_replace("HTTP/1.1 100 Continue\r\n\r\n","",$response);
	$ar = explode("\r\n\r\n", $response, 2);
//	$header = $ar[0];
//	$body = $ar[1];

	return $ar[1];
}

private static function get_time() {
	return floor(microtime(true)*1000);
}

private static function get_dmm_tokens() {
	$result = array();

	// Get login page
	$data = self::request(self::DMM_LOGIN_URL);

	// Search for certain data
	$matches = array();
	if (preg_match(self::DMM_TOKEN_PATTERN, $data, $matches)) {
		$result['dmm_token']=$matches[1];
	} else {
		throw new Exception("Cannot find DMM Token", 1);
	}
	if (preg_match(self::TOKEN_PATTERN, $data)) {
		$result['token']=$matches[1];
	} else {
		throw new Exception("Cannot find Token", 1);

	}
	return $result;
}

private static function get_ajax_tokens($dmm_token, $token) {
	$result = array();

	$headers = array('DMM_TOKEN: '. $dmm_token,
					 'Referer: '. self::DMM_LOGIN_URL,
					 'X-Requested-With: '. 'XMLHttpRequest');

	$data = self::request(self::AJAX_TOKEN_URL, array("token"=>$token), $headers);
	if ($data = json_decode($data, true)) {
		$result['id_token'] = $data['token'];
		$result['idKey'] = $data['login_id'];
		$result['pwKey'] = $data['password'];
	} else {
		throw new Exception("Invalid ajax token response: not json", 1);
	}

	return $result;
}


private static function get_osapi($login_id, $password, $id_token, $idKey, $pwKey) {
	$post_data = array (
		'token'=> $id_token,
		'login_id'=> $login_id,
		'save_login_id'=> '0',
		'password'=> $password,
		'save_password'=> '0',
		'use_auto_login'=> '0',
		$idKey => $login_id,
		$pwKey => $password,
		'path'=> '',
		'prompt'=> '',
		'client_id'=> '',
		'display'=> ''
	);
	$headers = array(
	                 'Referer: '. self::DMM_LOGIN_URL,
	                 'Origin: https://www.dmm.com'
	                 );
	$data = self::request(self::DMM_AUTH_URL, $post_data, $headers);
//	echo $data;
	$data = self::request(self::GAME_URL, NULL, $headers);
	$matches = array();
	if (preg_match(self::OSAPI_URL_PATTERN, $data, $matches)) {
		return $matches[1];
	} else {
		//var_dump($post_data);
		throw new Exception("Invalid login credentials", 1);
	}
}

private static function parse_osapi_url($osapi_url) {
	$result = array();
	$urlparts = explode("?", $osapi_url, 2);
	$params = explode("&", $urlparts[1]);
	foreach ($params as $param) {
		$kv = explode("=", $param, 2);
		if ($kv[0]==="owner") {
			$result['owner'] = $kv[1];
		}
		if ($kv[0]==="st") {
			$result['st'] = $kv[1];
		}
	}
	return $result;
}


private static function get_world_id($owner, $osapi_url) {
	$url = sprintf(self::WORLD_URL, $owner, self::get_time());
	$headers = array('Referer: '. $osapi_url);
	$data = this::request($url, NULL, $headers);
	$data = substr($data, strlen("svdata="));
	if ($svdata = json_decode($data, true));
	if ($svdata['api_result']===1) {
		return $svdata['api_data']['api_world_id'];
	} else {
		throw new Exception("Fail to get world_id: ".$svdata['api_result'], 1);
	}
}


private static function get_api_tokens($world_id, $owner, $st) {
	$result = array();

	$url = sprintf(self::FLASH_URL, self::$WORLD_IP[$world_id-1], $owner, self::get_time());
	$post_data = array(
		'url'=> $url,
		'httpMethod'=> 'GET',
		'authz'=> 'signed',
		'st'=> $st,
		'contentType'=> 'JSON',
		'numEntries'=> '3',
		'getSummaries'=> 'false',
		'signOwner'=> 'true',
		'signViewer'=> 'true',
		'gadget'=> 'http=>//203.104.209.7/gadget.xml',
		'container'=> 'dmm'
	);
	$data = self::request(self::MAKE_REQUEST_URL, $post_data, $headers);
	$data = substr($data, strlen("throw 1; < don't be evil' >"));
	if ($rsp = json_decode($data, true)) {
		if ($rsp[$url]['rc']!=200) {
			throw new Exception("Cannot get API Token: Remote server failed: ".$rsp[$url]['rc'], 1);
		}
		if ($svdata = json_decode(substr($rsp[$url]['body'], strlen("svdata=")))) {
			if ($svdata['api_result']===1) {
				$result['api_token'] = $svdata['api_token'];
				$result['api_starttime'] = $svdata['api_starttime'];
				return $result;
			} else {
				throw new Exception("Cannot get API token: You are banned", 1);
			}
		} else {
			throw new Exception("Cannot get API token: Malformed remote response", 1);
		}
	} else {
		throw new Exception("Cannot get API token: Malformed response", 1);
	}
}


public static function get_osapi_url($login_id, $password) {
//	dmm_token, token = get_dmm_tokens(s,)
	$dmmtokens = self::get_dmm_tokens();
	$dmm_token = $dmmtokens['dmm_token'];
	$token = $dmmtokens['token'];

//	id_token, idKey, pwKey = get_ajax_tokens(s, dmm_token, token)
	$ajaxtokens = self::get_ajax_tokens($dmm_token, $token);
	$id_token = $ajaxtokens['id_token'];
	$idKey = $ajaxtokens['idKey'];
	$pwKey = $ajaxtokens['pwKey'];

//	osapi_url = get_osapi(s, login_id, password, id_token, idKey, pwKey)
	$osapi_url = self::get_osapi($login_id, $password, $id_token, $idKey, $pwKey);

	return $osapi_url;
}


public static function login($login_id, $password) {
//	dmm_token, token = get_dmm_tokens(s,)
	$dmmtokens = self::get_dmm_tokens();
	$dmm_token = $dmmtokens['dmm_token'];
	$token = $dmmtokens['token'];

//	id_token, idKey, pwKey = get_ajax_tokens(s, dmm_token, token)
	$ajaxtokens = self::get_ajax_tokens($dmm_token, $token);
	$id_token = $ajaxtokens['id_token'];
	$idKey = $ajaxtokens['idKey'];
	$pwKey = $ajaxtokens['pwKey'];

//	osapi_url = get_osapi(s, login_id, password, id_token, idKey, pwKey)
	$osapi_url = self::get_osapi($login_id, $password, $id_token, $idKey, $pwKey);

//	owner, st = parse_osapi_url(osapi_url)
	$osapiurl_components = self::parse_osapi_url($osapi_url);
	$owner = $osapiurl_components['owner'];
	$st = $osapiurl_components['st'];

//	world_id = get_world_id(s, owner, osapi_url)
	$world_id = self::get_world_id($owner, $osapi_url);

//	world_ip = WORLD_IP[world_id-1]
	$world_ip = self::$WORLD_IP[$world_id-1];

//	api_token, api_starttime = get_api_tokens(s, world_id, owner, st)
	$apitokens = self::get_api_tokens();
	$api_token = $apitokens['api_token'];
	$api_starttime = $apitokens['api_starttime'];

//	flash_url = 'http://%s/kcs/mainD2.swf?api_token=%s&amp;api_starttime=%d' % \
//				(world_ip, api_token, api_starttime)

	$result = array(
	                 "server"=>$world_ip,
	                 "token"=>$api_token,
	                 "starttime"=>$api_starttime,
	                 "dmmid"=>$owner
	                 );

	return $result;
}


}
