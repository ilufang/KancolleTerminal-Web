<?php
/**
 *	KCRequest
 *
 *	Integrated handler for handling api requests
 *
 *	2015 by ilufang
 */

require_once "../KCForwardUser.class.php";
require_once "../config.php";

require_once 'KCLogger.class.php';
require_once 'KCFurnitureHacks.class.php';
require_once 'KCAPIPerm.class.php';

class KCRequest {

// Pre-process parameters
public $uri, $post, $headers;
// Request type, string, values are:
// REQUEST:	Forward this request to the server
// REWRITE:	Process by the internal kc engine
// SUCCESS:	Completed request
// FAILURE:	Bad request
public $req_type,$errno=0,$errmsg;
// Request response, json array (exclude api_status)
public $response;
// User session
public $user;

public $furnhack;

/**
 *	Constructor
 */
function __construct($uri, $post, $headers) {
	$this->uri = $uri;
	$this->post = $post;
	$this->headers = $headers;
	// Determine req_type
	$user = new KCUser();
	if ($user->initWithToken($this->post["api_token"])) {
	//var_dump($user);
		switch ($user->gamemode) {
			case 3:
				$this->user = new KCForwardUser($user);
				$this->furnhack = new KCFurnitureHacks($this->user);
				$dmmnames = json_decode(file_get_contents("dmm-names.json"),true);
				$gamename = $dmmnames[$this->user->dmmid];
				require_once '../ban/KCBan.class.php';
				$ban = new KCBan("../ban.json");
				if ($ban->isBanned($gamename)) {
					$this->errno = 4012;
					break;
				}
				$this->req_type = "REQUEST";
				break;
			default:
				$this->errno = 4011;
				break;
		}
	} else {
		$this->errno = 4010;
	}

}


/**
 *	forwardRequest
 *
 *	Forward current request to user's kadokawa server
 *	Write response
 */
function forwardRequest() {
	// Generate request url
	$server = $this->user->server;
	$url = "http://$server".$this->uri;

	// Curl
	$curlSession = curl_init();
	curl_setopt($curlSession, CURLOPT_URL, $url);
	curl_setopt($curlSession, CURLOPT_HEADER, 1);

	// Post arguments
	curl_setopt($curlSession, CURLOPT_POST, 1);
	curl_setopt($curlSession, CURLOPT_POSTFIELDS, file_get_contents("php://input")); // TODO
	curl_setopt($curlSession,CURLOPT_ENCODING, '');
	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);

	// Timeout fix to 3 min: Kadokawa server load
	curl_setopt($curlSession, CURLOPT_TIMEOUT, 180);
	set_time_limit(180);

	// Process headers
	// TODO replace some headers
	$headers = array();
	global $config;
	foreach (getallheaders() as $key => $value) {
		$value = str_ireplace($config["serveraddr"], $server, $value);
		$value = str_ireplace("home.php", "kcs/mainD2.swf?api_token=".$this->post["api_token"], $value);
		$headers[] = "$key: $value";
	}

	curl_setopt($curlSession, CURLOPT_HTTPHEADER, $headers);

	//Send the request and store the result in an array
	$response = curl_exec ($curlSession);

	// Check that a connection was made
	if (curl_error($curlSession)){
	        // If it wasn't...
	        $this->req_type = "FAILURE";
	        $this->errmsg = $url."|".curl_error($curlSession);
	        $this->errno = 500;
	        return;
	} else {

		//clean duplicate header that seems to appear on fastcgi with output buffer on some servers!!
		$response = str_replace("HTTP/1.1 100 Continue\r\n\r\n","",$response);

		$ar = explode("\r\n\r\n", $response, 2);


		$header = $ar[0];
		$body = $ar[1];

		//handle headers - simply re-outputing them
		//file_put_contents("header.log", $header);
		$header_ar = split(chr(10),$header);
		foreach($header_ar as $k=>$v){
			if(!( preg_match("/^Transfer-Encoding/",$v) || preg_match("/^Content-Encoding/",$v) )){
				$v = str_replace($server,$config["serveraddr"],$v); //header rewrite if needed
				header(trim($v));
			}
		}

		// Parse body
		if (substr($body, 0, strlen("svdata"))!=="svdata") {
			$body = gzdecode($body);
		}


		$data = json_decode(substr($body, strlen("svdata=")),true);
		if ($data["api_result"]!=1) {
			$this->req_type = "FAILURE";
			$this->errno = $data["api_result"];
			return;
		} else {
			$this->response = $data["api_data"];
			$this->errno = 1;
		}
	}
	curl_close ($curlSession);
}


/**
 *	request
 *
 *	Make the request
 */
function request() {
	if ($this->req_type === "REQUEST") {
		$this->forwardRequest();
	} else if ($this->req_type === "REWRITE") {
		// TODO: Link with local server
	}
}


/**
 *	beforeRequest
 *
 *	Handle, or modify the request contents before it is being processed, either by internal engine or forwarded kadokawa server
 *	May reset the request by setting $req_type to a completed state, either SUCCESS or FAILURE
 */
function beforeRequest() {
	// TODO
	$this->furnhack->beforeRequest($this);

	KCAPIPerm::beforeRequest($this);

	// start2 emergency solution
	if (explode("?",$this->uri)[0]==="/kcsapi/api_start2" && file_exists("start2.json")) {
		$this->response = json_decode(file_get_contents("start2.json"),true);
		if ($this->response) {
			$this->req_type = "REWRITTEN";
			$this->errno = 1;
		} else {
			unlink("start2.json");
		}
	}

}

/**
 *	afterRequest
 *
 *	Handle, log or modify the request or response after a response has been generated
 */
function afterRequest() {
	// TODO
	if ($this->errno ==1 ) {
		require_once 'KCViewer.class.php';
		$user = new KCUser();
		$user->initWithToken($this->post['api_token']);
		$viewer = new KCViewer($user);
		$viewer->afterRequest($this);

		KCLogger::request($this);
		$this->furnhack->afterRequest($this);
	}

	// start2 emergency solution
	if ($this->uri==="/kcsapi/api_start2" && !file_exists("start2.json")) {
		file_put_contents("start2.json", json_encode($this->response));
	}
}

/**
 *	replaceKCAcessArgs
 *
 *	replace %{} variables
 *
 *	@return The argument after replacement
 */
function replaceKCAcessArgs($str) {
	$str = str_ireplace("%{REQUEST_URI}", $this->uri, $str);
	$str = str_ireplace("%{QUERY_STRING}", file_get_contents("php://input"), $str); // TODO
	return $str;
}

/**
 *	encodeUTFEntities
 *
 *	Replace characters with \uxxxx
 *	Using json_encode
 *
 *	@return The utf-escaped string
 */
function encodeUTFEntities($str) {
	$len = strlen($str);
	$str = json_encode("$str");
	$str = substr($str, 1, -1);
	$str = str_replace('\"', '"', $str);
	return $str;
}

/**
 *	furniture
 *
 *	Enable all furniture for replacing furniture packets
 *	@deprecated Replaced by KCFurnitureHacks class
 */

/*
function furniture($data) {
	if (strcasecmp($this->uri,"/kcsapi/api_get_member/furniture")!=0){
		return $data;
	}
	$obj = json_decode($data, true);
	$memberid = $obj[0]['api_member_id'];
	$db = json_decode(file_get_contents("gamedb.json"), true);
	$full_furn = array();
	foreach ($db['furniture'] as $key => $value) {
		$full_furn[] = array("api_member_id"=>$memberid,
		                     "api_id"=>$key,
		                     "api_furniture_type"=>$value['api_type'],
		                     "api_furniture_no"=>$value['api_no'],
		                     "api_furniture_id"=>$key
		                     );
	}
	return json_encode(array("api_result"=>1, "api_result_msg"=>"Successfully modified furniture data", "api_data"=>$full_furn));
}
*/

/**
 *	translate
 *
 *	Raw string replace right before returning the response
 *	Should be used on gamemode=3 (or other mode that $user->kcaccess exists)
 */
function translate($data) {
	if (!isset($this->user->kcaccess)) {
		return $data;
	}
	$cond_subject = " ";
	$cond_rule = "(.*)";
	foreach ($this->user->kcaccess as $entry) {
		$disabled = false;
		foreach (explode(" ", $entry['option']) as $option) {
			switch ($option) {
				case "!":
					$disabled = true;
			}
		}
		if ($disabled) {
			continue;
		}
		$entry["arg1"] = $this->replaceKCAcessArgs($entry["arg1"]);
		$entry["arg2"] = $this->replaceKCAcessArgs($entry["arg2"]);
		switch ($entry["type"]) {
			/*
			case 'furniture':
				$data = $this->furniture($data);
				break;
			*/
			case "translate":
			case "replace":
				if (preg_match($cond_rule, $cond_subject)!=0) {
					if ($entry["arg1"][0]==='%') {
						$transdb = json_decode(file_get_contents("transdb.json"),true);
						if (isset($transdb[$entry["arg2"]][$entry["arg1"]])) {
							foreach ($transdb[$entry["arg2"]][$entry["arg1"]] as $key => $value) {
								$type = $entry["arg1"];
								$find = $this->encodeUTFEntities($key);
								$repl = $value;
								if ($type==="%equip") {
									$data = str_replace("\"$find\"", "\"$repl\"", $data);
								} else if ($type==="%ship") {
									//$data = str_replace("$find", "$repl", $data);

									$data = str_replace("\"$find", "\"$repl", $data);
									$data = str_replace("\u300c$find", "\u300c$repl", $data);
									$data = str_replace("_$find", "$repl", $data);
								} else {
									$data = str_replace("$find", "$repl", $data);
								}
							}
						} else if ($entry['arg1']==='%*') {
							foreach ($transdb[$entry['arg2']] as $type => $translations) {
								foreach ($translations as $key => $value) {
									$find = $this->encodeUTFEntities($key);
									$repl = $value;
									if ($type==="%equip") {
										$data = str_replace("\"$find\"", "\"$repl\"", $data);
									} else if ($type==="%ship") {
										//$data = str_replace("$find", "$repl", $data);

										$data = str_replace("\"$find", "\"$repl", $data);
										$data = str_replace("\u300c$find", "\u300c$repl", $data);
										$data = str_replace("_$find", "$repl", $data);
									} else {
										$data = str_replace("$find", "$repl", $data);
									}
								}
							}
						}
					} else {
						$data = str_replace($this->encodeUTFEntities($entry["arg1"]), $entry["arg2"], $data);
					}
				}
				$cond_subject = " ";
				$cond_rule = "(.*)";
				break;
			case 'PregReplace':
				if (preg_match("/$cond_rule/", $cond_subject)!=0) {
					$data = preg_replace("/$entry[arg1]/", $entry["arg2"], $data);
				}
				$cond_subject = " ";
				$cond_rule = "(.*)";
				break;
			case 'PregMatch':
				$cond_subject = $entry["arg1"];
				$cond_rule = $entry["arg2"];
				break;
		}
		foreach (explode(",", $entry['option']) as $option) {
			switch ($option) {

			}
		}
	}
	return $data;
}

/**
 *	generateResponseString
 *
 *	Encode array as json and add aux info, readable by kancolle swf
 */
function generateResponseString() {
	$result_msg = "Unknown error. Unrecognized error code.";
	switch ($this->errno) {
		case 0:
			$result_msg = "Script Error. Request is not flagged as processed.";
			break;
		case 1:
			$result_msg = "Success";
			break;
		case 100:
			$result_msg = "Invalid request. (Please refresh page)";
			break;
		case 200:
			$result_msg = "Request failed. (Server Maintenance)";
			break;
		case 201:
			$result_msg = "Illegal request. (Please refresh page. A new token may be required)";
			break;
		case 500:
			$result_msg = "Curl Error: ".$this->errmsg;
			break;
		case 4010:
			$result_msg = "Authentication required. Cannot process requests from unknown user: ".$this->post["api_token"];
			break;
		case 4011:
			$result_msg = "Unknown gamemode";
			break;
	}

	if ($this->errno != 1) {
		$errlog = fopen("error.log", "a");
		$erruser = $this->post['api_token'];
		if (isset($this->user)) {
			$dmmnames = json_decode(file_get_contents("dmm-names.json"),true);
			$erruser = $dmmnames[$this->user->dmmid];
		}
		date_default_timezone_set("Asia/Shanghai");
		fwrite($errlog, "[".date("Y-m-d h:i:s")."] $erruser ".$this->errno."($_SERVER[REQUEST_URI])\n");
		foreach ($this->post as $key => $value) {
			fwrite($errlog, "$key: $value\n");
		}
		fwrite($errlog, "\n");
		fclose($errlog);
	}


	$response = array("api_result"=>$this->errno, "api_result_msg"=>$result_msg, "api_data"=>$this->response);
	$json = $this->translate(json_encode($response));
	return "svdata=$json";
}


/**
 *	printResponse
 *
 *	Make request and sends to output. (Interface entry point)
 */
function printResponse() {
	$this->beforeRequest();
	$this->request();
	$this->afterRequest();

	// Print to output
	// header("Content-Type: text/plain");
	echo $this->generateResponseString();
}


}
