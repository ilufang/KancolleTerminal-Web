<?php
/**
 *	frames
 *
 *	Load game with inspector, just like any other viewer software
 *
 *	2015 by ilufang
 */
require_once 'config.php';
?><!DOCTYPE html>
<html>
<head><title><?=$config['title']?></title></head>
<frameset cols="60%,40%" border="2" bordercolor="#666">
<frame src="/?v=<?php
// Loads the last used interface
if (isset($_COOKIE["pref_interface"])) {
	echo $_COOKIE["pref_interface"];
} else {
	echo "flash";
}
?>"></frame>
<frame src="/ii"></frame>
</frameset>
</html>
