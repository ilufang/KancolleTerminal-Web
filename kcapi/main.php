<?php
/**
 *	kcapi/main.php
 *
 *	API entry point
 *
 * 2015 by ilufang
 */

require_once "KCRequest.class.php";

header("Content-Type: text/plain");

date_default_timezone_set('Asia/Shanghai');

$req = new KCRequest($_SERVER["REQUEST_URI"], $_REQUEST, getallheaders());
echo $req->printResponse();
