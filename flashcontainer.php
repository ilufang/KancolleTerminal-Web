<?
/**
 *	flash container
 *
 *	Run the game in the DMM's flash size
 *
 *	2015 by ilufang
 */
require_once 'config.php';
?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title><?=$config['title']?></title>
	<style type="text/css">
	body {
		background-color: black;
	}
	</style>
</head>
<body>
<?php
$flashurl="/kcs/mainD2.swf?api_token=$_REQUEST[token]&api_starttime=$_REQUEST[starttime]";
?>
<center>
	<div id="flashWrap" style="display:inline-block">
		<embed src="<?php echo $flashurl;?>" type="application/x-shockwave-flash" width="800" height="480" base="/kcs/" id="externalswf"/>
	</div>
</center>
</body>
</html>
