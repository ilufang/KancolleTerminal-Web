<?php
require_once 'dmmLogin.class.php';
//header("Content-Type: text/plain");

try {
	$logins = dmmLogin::login("cc2lufang@126.com","19971220lf");
	var_dump($logins);
} catch(Exception $e) {
	echo $e->getMessage();
	echo $e->getTraceAsString();
}
?>
