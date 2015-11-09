<?php
/**
 *	kcres/main.php
 *
 *	Static resources entry point
 *
 * 2015 by ilufang
 */

require_once 'KCResource.class.php';

$version = false;

foreach ($_REQUEST as $key => $value) {
	if (strcasecmp($key, "version")==0) {
		$version = $value;
	}
}

$req_resource = new KCResource($_SERVER["REQUEST_URI"],$version);
if (!$req_resource->init()) {
	header("HTTP/1.0 404 Not Found");
	die();
}
$req_resource->printResponse();
