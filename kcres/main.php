<?php
/**
 *	kcres/main.php
 *
 *	Static resources entry point
 *
 * 2015 by ilufang
 */

require_once 'KCResource.class.php';

$req_resource = new KCResource($_SERVER["REQUEST_URI"],$_REQUEST["version"]);
$req_resource->printResponse();
