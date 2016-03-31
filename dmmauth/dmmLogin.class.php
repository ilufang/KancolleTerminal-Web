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

const DMM_TOKEN_PATTERN = '/"DMM_TOKEN", "([\d|\w]+)"/';
const TOKEN_PATTERN = '/"token": "([\d|\w]+)"/';
const OSAPI_URL_PATTERN = '/URL\W+:\W+"(.*)",/';

const AUTH_ERRMSG_PATTERN = '/<div class="box-txt-error"><p>(.*)<\/p><\/div>/';

private static $cookies;
private static $session;

private static function cookie_parse($cookie) {
	$ckpair = array();
	$lines = explode(";", $cookie);
	foreach ($lines as $l) {
		$pair = explode("=", $l, 2);
		$ckpair[trim($pair[0])] = trim($pair[1]);
	}
	return $ckpair;
}

private static function cookie_make($cookie) {
	$cklines = array();
	foreach ($cookie as $k => $v) {
		$cklines[] = "$k=$v";
	}
	return implode("; ", $cklines);
}

private static function request($url, $postdata = NULL, $headers = NULL) {
	// Make curl request

	// echo "> $url\n\n"; // Debug

	// Force write regional cookies
	self::$cookies['ckcy'] = '1';
	self::$cookies['cklg'] = 'ja';

	set_time_limit(self::REQUESTS_TIMEOUT);
	$s = self::$session;
	curl_setopt($s, CURLOPT_URL, $url);

	// Set headers if provided
	if (isset($headers)) {
		curl_setopt($s, CURLOPT_HTTPHEADER, $headers);
	}

	// Set Cookies
	curl_setopt($s, CURLOPT_COOKIE, self::cookie_make(self::$cookies));

	// Set post if provided
	if (isset($postdata)) {
		$urlencoded = array();
		foreach ($postdata as $key => $value) {
			$urlencoded[] = urlencode($key).'='.urlencode($value);
		}
		curl_setopt ($s, CURLOPT_POST, true);
		curl_setopt ($s, CURLOPT_POSTFIELDS, implode('&', $urlencoded));
	}

	$response = curl_exec($s);

	/*
	// Debug
	print(curl_getinfo($s)['request_header']);
	if (isset($postdata)) {
		var_export($postdata);
		echo "\n\n";
	}
	*/

	if (curl_error($s)) {
		throw new Exception("CURL请求失败: ".curl_error($s), 1);
	}
	$response = str_replace("HTTP/1.1 100 Continue\r\n\r\n","",$response);
	$ar = explode("\r\n\r\n", $response, 2);
	$header = $ar[0];
	$body = $ar[1];

	// Scan returned headers for set-cookies
	$lines = explode("\n", $header);
	$redir = false;
	foreach ($lines as $l) {
		$pair = explode(':', $l, 2);
		if (count($pair)==2) {
			$k = trim($pair[0]);
			$v = trim($pair[1]);
			if (strcasecmp($k, "Set-Cookie") === 0) {
				$cknew = trim(explode(';', $v)[0]);
				$cknew = explode('=', $cknew);
				self::$cookies[trim($cknew[0])] = trim($cknew[1]);
			} else if (strcasecmp($k, 'Location') === 0) {
				// Register redirect
				$redir = trim($v);
			}
		}
		// echo $l; // Debug
	}

	/*
	// Debug
	if ($url !== self::DMM_LOGIN_URL) {
		echo "\n\n$body\n";
	} else {
		echo "\n\nBody Skipped\n";
	}
	*/

	if ($redir) {
		$targetinfo = parse_url($redir);
		if (!isset($targetinfo['host'])) {
			$urlinfo = parse_url($url);
			$redir = "$urlinfo[scheme]://$urlinfo[host]$redir";
		}
		return self::request($redir, $postdata, $headers);
	}

	// echo "\n==============================\n"; // Debug

	return $body;
}

private static function get_time() {
	return floor(microtime(true)*1000);
}

private static function get_dmm_tokens() {
	$result = array();

	// Get login page
	$data = self::request(self::DMM_LOGIN_URL, NULL, array('Connection: keep-alive','Keep-Alive: 300'));

	// Search for token patterns
	$matches = array();
	if (preg_match(self::DMM_TOKEN_PATTERN, $data, $matches)) {
		$result['dmm_token']=$matches[1];
	} else {
		throw new Exception("无法获取登陆用DMM_TOKEN", 1);
	}
	if (preg_match(self::TOKEN_PATTERN, $data, $matches)) {
		$result['token']=$matches[1];
	} else {
		throw new Exception("无法获取登陆用TOKEN", 1);

	}
	return $result;
}

private static function get_ajax_tokens($dmm_token, $token) {
	$result = array();

	$headers = array(
		'DMM_TOKEN: '. $dmm_token,
		'Referer: '. self::DMM_LOGIN_URL,
		'X-Requested-With: '. 'XMLHttpRequest',
		'Connection: keep-alive',
		'Keep-Alive: 300'
	);

	$data = self::request(self::AJAX_TOKEN_URL, array("token"=>$token), $headers);

	if ($data = json_decode($data, true)) {
		$result['id_token'] = $data['token'];
		$result['idKey'] = $data['login_id'];
		$result['pwKey'] = $data['password'];
	} else {
		throw new Exception("无法获取登陆用AJAX_TOKEN: 返回的JSON无法解析\n".$data, 1);
	}

	return $result;
}

private static function get_osapi($login_id, $password, $id_token, $idKey, $pwKey) {
	$post_data = array(
		'login_id'=> $login_id,
		'save_login_id'=> '0',
		'password'=> $password,
		'save_password'=> '0',
		'token'=> $id_token,
		$idKey => $login_id,
		$pwKey => $password,
		'path'=> ''
	);
	$headers = array(
		'Referer: '. self::DMM_LOGIN_URL,
		'Origin: https://www.dmm.com',
		'Connection: keep-alive',
		'Keep-Alive: 300'
	);

	// Post auth
	$data = self::request(self::DMM_AUTH_URL, $post_data, $headers);

	$matches = array();
	if (preg_match(self::AUTH_ERRMSG_PATTERN, $data, $matches)) {
		// Login failed with a message
		throw new Exception("登录失败: ".$matches[1], 1);
	}

	// Visit game url and search for osapi pattern
	$data = self::request(self::GAME_URL, NULL, $headers);
	if (preg_match(self::OSAPI_URL_PATTERN, $data, $matches)) {
		return $matches[1];
	} else {
		throw new Exception("登录失败. 发生未知错误, 可能是DMM要求您修改密码", 1);
	}
}

private static function parse_osapi_url($osapi_url) {
	$result = array();
	$urlparts = parse_url($osapi_url);
	$params = explode("&", $urlparts['query']);

	// Get owner and st from query string
	foreach ($params as $param) {
		$kv = explode("=", $param, 2);
		if ($kv[0]==="owner") {
			$result['owner'] = urldecode($kv[1]);
		}
		if ($kv[0]==="st") {
			$result['st'] = urldecode($kv[1]);
		}
	}
	return $result;
}


private static function get_world_id($owner, $osapi_url) {
	// Get player server
	$url = sprintf(self::WORLD_URL, $owner, self::get_time());
	$headers = array('Referer: '. $osapi_url);
	$data = self::request($url, NULL, $headers);
	$data = substr($data, strlen("svdata="));
	$svdata = json_decode($data, true);
	if ($svdata && $svdata['api_result']==1) {
		return $svdata['api_data']['api_world_id'];
	} else {
		throw new Exception("获取玩家所在服务器失败: ".$svdata['api_result'], 1);
	}
}


private static function get_api_tokens($world_id, $owner, $st, $ref) {
	$result = array();

	$headers = array(
		'Connection: keep-alive',
		'Keep-Alive: 300',
		"Referer: $ref"
	);

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
		'gadget'=> 'http://203.104.209.7/gadget.xml',
		'container'=> 'dmm'
	);
	$data = self::request(self::MAKE_REQUEST_URL, $post_data, $headers);
	$data = substr($data, strlen("throw 1; < don't be evil' >"));
	if ($rsp = json_decode($data, true)) {
		if ($rsp[$url]['rc']!=200) {
			throw new Exception("Cannot get API Token: Remote server failed: ".$rsp[$url]['rc'], 1);
		}
		if ($svdata = json_decode(substr($rsp[$url]['body'], strlen("svdata=")), true)) {
			if ($svdata['api_result']===1) {
				$result['api_token'] = $svdata['api_token'];
				$result['api_starttime'] = $svdata['api_starttime'];
				return $result;
			} else {
				throw new Exception('获取游戏token失败. 错误'.$svdata['api_result'].' 您可能已被封禁', 1);
			}
		} else {
			throw new Exception("获取游戏token失败. 返回的JSON无法解析\n".$rsp[$url]['body'], 1);
		}
	} else {
		throw new Exception("获取游戏token失败. 返回的JSON无法解析\n$data", 1);
	}
}

/**
 *	login
 *
 *	Entry point. Get login information with id and password
 */
public static function login($login_id, $password) {
	try {

		// Initialize curl session
		self::$session = curl_init();
		$s = self::$session;
		curl_setopt($s, CURLOPT_HEADER, TRUE);
		curl_setopt($s, CURLOPT_USERAGENT, self::REQUESTS_USER_AGENT);
		curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($s, CURLOPT_FOLLOWLOCATION, FALSE);
		curl_setopt($s, CURLOPT_TIMEOUT, self::REQUESTS_TIMEOUT);
		curl_setopt($s, CURLOPT_ENCODING, '');
		curl_setopt($s, CURLINFO_HEADER_OUT, true);

		// Set regional cookie flags
		self::$cookies = array();

		// Get DMM tokens
		$dmmtokens = self::get_dmm_tokens();
		$dmm_token = $dmmtokens['dmm_token'];
		$token = $dmmtokens['token'];

		// Get Ajax token
		$ajaxtokens = self::get_ajax_tokens($dmm_token, $token);
		$id_token = $ajaxtokens['id_token'];
		$idKey = $ajaxtokens['idKey'];
		$pwKey = $ajaxtokens['pwKey'];

		// Perform login and get OSAPI url
		$osapi_url = self::get_osapi($login_id, $password, $id_token, $idKey, $pwKey);
		$osapiurl_components = self::parse_osapi_url($osapi_url);
		$owner = $osapiurl_components['owner'];
		$st = $osapiurl_components['st'];

		// Get Player server worldID
		$world_id = self::get_world_id($owner, $osapi_url);

		// Get game tokens
		$apitokens = self::get_api_tokens($world_id, $owner, $st, $osapi_url);
		$api_token = $apitokens['api_token'];
		$api_starttime = $apitokens['api_starttime'];

		// Results
		$result = array(
			'status'=>		'success',
			'serverip'=>	self::$WORLD_IP[$world_id-1],
			'token'=>		$api_token,
			'starttime'=>	$api_starttime,
			'owner'=>		$owner
		);
		return $result;
	} catch (Exception $e) {
		return array(
			'status' => $e->getMessage()
		);
	}
}


}
