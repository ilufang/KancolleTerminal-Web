<?
/**
 *	flash object
 *
 *	Run the game in the DMM's flash size using <object>
 *	For testing purposes only. Use flashcontainer.php.
 *
 *	2015 by ilufang
 */
require_once 'config.php';
?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title><?=$config['title']?></title>
</head>
<body>
<?php
$flashurl="/kcs/mainD2.swf?api_token=$_REQUEST[token]&api_starttime=$_REQUEST[starttime]";
?>
<center>
	<object type="application/x-shockwave-flash" width="800" height="480" data="<?php echo $flashurl;?>" base="/kcs/"></object>
</center>
</body>
</html>
