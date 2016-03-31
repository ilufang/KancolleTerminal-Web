<?php
/**
 *	ssl
 *
 *	Confirm SSL or plain connection
 *	This page was originally for downloading the SSL certificate as that on kc.nfls.ga was self signed
 *
 *	2015 by ilufang
 */

require_once 'config.php';
?><!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title><?=$config['title']?> SSL</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript">
	function redir(protocol) {
		$.cookie("ssl", protocol, { expires: 365, path: '/' });
		window.location.assign(protocol+"://<?=$config['serveraddr']?>");
	}

	</script>
</head>
<body>
<div class="toolbar"><h1><?=$config['title']?> SSL</h1></div>
<br><br>
<div class="box" style="width:80%">
	<h2>您即将通过HTTPS访问本站</h2>
	<p>使用HTTPS将会使用SSL加密您与服务器之间的<strong>所有</strong>数据. 使用SSL可以:</p>
	<ul>
		<li>保护您的登录数据不被第三方抓改包</li>
		<li>重要! 当您使用DMM登录时, 保护您的DMM用户名和密码不被第三方抓改包</li>
		<li>保护您的Flash链接, 包括游戏token, 不被第三方抓改包</li>
		<li>保护您的游戏不被第三方抓改包</li>
	</ul>
	<strong>请注意! 由于游戏数据也会被加密, 使用HTTPS时第三方浏览器(如KCV, Poi, 但不包括KC3改)可能将无法正常运行! 请务必在这些使用HTTP连接</strong>
	<hr />
	<input type="button" value="使用HTTPS访问" onclick="redir('https')"/>   <input type="button" value="使用HTTP访问" onclick="redir('http')"/>
</div>
</body>
</html>
