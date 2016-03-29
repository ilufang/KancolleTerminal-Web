<?php
function tryModified($filename) {
	if (file_exists($filename)) {
		$sha = sha1_file($filename);
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === "\"$sha\"") {
			header("HTTP/1.1 304 Not Modified");
			die();
		}
		header("Etag: \"$sha\"");
	}
}
?>
