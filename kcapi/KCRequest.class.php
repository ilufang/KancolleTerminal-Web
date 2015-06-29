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


class KCRequest {

// Pre-process parameters
private $uri, $post, $headers;
// Request type, string, values are:
// REQUEST:	Forward this request to the server
// REWRITE:	Process by the internal kc engine
// SUCCESS:	Completed request
// FAILURE:	Bad request
private $req_type,$errno=0,$errmsg;
// Request response, json array (exclude api_status)
private $response;
// User session
private $user;

/**
 *	Constructor
 */
function __construct($uri, $post, $headers) {
	$this->uri = $uri;
	$this->post = $post;
	$this->headers = $headers;

	// Determine req_type
	$user = new KCUser();
	if ($user->initWithToken($_REQUEST["api_token"])) {
		switch ($user->gamemode) {
			case 3:
				$this->user = new KCForwardUser($user);
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
	curl_setopt ($curlSession, CURLOPT_URL, $url);
	curl_setopt ($curlSession, CURLOPT_HEADER, 1);

	// Post arguments
	curl_setopt ($curlSession, CURLOPT_POST, 1);
	curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $this->post);

	curl_setopt($curlSession, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curlSession, CURLOPT_TIMEOUT,30);
	curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 1);

	// Process headers
	// TODO replace some headers
	$headers = array();
	global $config;
	foreach (getallheaders() as $key => $value) {
		$value = str_ireplace($config["serveraddr"], $server, $value);
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
		$header_ar = split(chr(10),$header); 
		foreach($header_ar as $k=>$v){
			if(!preg_match("/^Transfer-Encoding/",$v)){
				$v = str_replace($base,$mydomain,$v); //header rewrite if needed
				header(trim($v));
			}
		}
		// Parse body
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
}

/**
 *	afterRequest
 *
 *	Handle, log or modify the request or response after a response has been generated
 */
function afterRequest() {
	// TODO
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
		case 201:
			$result_msg = "Illegal request. (Please refresh page. A new token may be required)";
			break;
		case 500:
			$result_msg = "Curl Error: ".$this->errmsg;
			break;
		case 4010:
			$result_msg = "Authentication required. Cannot process requests from unknown user";
			break;
		case 4011:
			$result_msg = "Unknown gamemode";
			break;
	}

	$response = array("api_result"=>$this->errno, "api_result_msg"=>$result_msg, "api_data"=>$this->response);
	return "svdata=".json_encode($response);
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
	header("Content-Type: text/plain");
	echo $this->generateResponseString();
}


}
