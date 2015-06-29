<?php
/**
 *	kcapi/main.php
 *
 *	API entry point
 *
 * 2015 by ilufang
 */

require_once "KCRequest.class.php";

$req = new KCRequest($_SERVER["REQUEST_URI"], file_get_contents("php://input"), getallheaders());
$req->printResponse();
