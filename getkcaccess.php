<?php
require_once 'KCUser.class.php';
$user = new KCUser();
if (!$user->initWithToken($_REQUEST["token"])) {
	header("HTTP/1.0 401 Unauthorized");
	die("Authentication Failed");
}

require_once 'KCSql.class.php';

header("Content-Type: application/json");

$result = KCSql::inst()->select(array("kcaccess"),"forward_users")->where("memberid={$user->id}")->query();
if (isset($result[0])) {
	die($result[0]["kcaccess"]);
} else {
	die("[]");
}

?>
