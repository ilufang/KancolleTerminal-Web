<?php

require "entryinspect.php";

$url = substr($_SERVER['REQUEST_URI'],1); // Shift out preceding slash
$url = explode("?", $url)[0];
if (file_exists($url)) {
	$data = file_get_contents($url);
	if (isset($_REQUEST['raw'])) {
		header("Content-Type: application/json; charset=utf-8");
		echo $data;
	} else {
		header("Content-Type: text/plain; charset=utf-8");
		$json = json_decode($data, true);
		if ($json) {
			var_export($json);
		} else {
			echo $data."\n";
			echo "Parse Error:".json_last_error_msg();
		}
	}
} else {
	header("HTTP/1.0 404 Not Found");
	die("<h1>404 Not Found</h1>The requested file ($_SERVER[REQUEST_URI]) is not found on this server.<br />");
}
