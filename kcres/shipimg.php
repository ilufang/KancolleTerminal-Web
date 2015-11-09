<?php

if (isset($_REQUEST["id"])) {
	$idx = $_REQUEST["id"];
} else {
	header("Location: /ships.php");
	die();
}
$files = scandir("images/ship/$idx/");
natsort($files);
foreach ($files as $file) {
	if (strcasecmp(substr($file, -3), "png")==0) {
		echo "<img src='images/ship/$idx/$file' /><br />\n";
	}
}
