<?php

if (!preg_match("/^(http|https)\:\/\/$_SERVER[SERVER_NAME]\/home.php$/", $_SERVER['HTTP_REFERER'])) {
	// Not from home.php
	require_once 'KCUser.class.php';
	$user = new KCUser();
	if (!$user->initWithAuth($_COOKIE['username'], $_COOKIE['passhash'])) {
		// Not logged in
		header("HTTP/1.0 403 Forbidden");
		die("<h1>403 Forbidden</h1>You don't have permission to access this page. Please log in.");
	}
}
