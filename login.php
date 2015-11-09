<?php
/**
 *	index
 *
 *	Entry point
 *
 *	2015 by ilufang
 */

$protocol = "unset";

if (isset($_COOKIE['ssl'])) {
	$protocol = $_COOKIE['ssl'];
}

$active_protocol = "http";
if($_SERVER['HTTPS'] == 'on'){
	$active_protocol = "https";
}

if ($protocol==="http" && $active_protocol !== "http") {
	header("Location: http://kc.nfls.ga".$_SERVER['REQUEST_URI']);
	die();
}

if ($protocol==="https" && $active_protocol !== "https") {
	header("Location: https://kc.nfls.ga".$_SERVER['REQUEST_URI']);
	die();
}

if (isset($_COOKIE['username']) && isset($_COOKIE['passhash']) && !isset($_REQUEST['failure'])) {
	header("Location: /");
	die();
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kancolle Terminal</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/sha.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<style type="text/css">
	.loginmain {
		width: 40em;
		margin: 3em;
		display: flex;
		text-align: center;
		flex-direction: row;
		justify-content: space-around;
		flex-wrap: wrap;
	}
	.loginmain div {
		width: 12em;
	}
	</style>
</head>
<body>
	<div class="toolbar">
		<h1>Kancolle Terminal</h1>
		<div>
			<a href="ssl.html">切换HTTP/HTTPS</a>
		</div>
	</div>
	<center>
	<div class="loginmain">
		<div class="box">
			<form onsubmit="login();return false;">
			<h2>登录</h2>
				<input type="text" id="username" placeholder="用户名" required /><br />
				<input type="password" id="password" placeholder="密码" required /><br />
				<p>需要启用Cookies</p>
				<input type="submit" value="Login"/>
			</form>
		</div>
		<div class="box">
			<h2>没有账号?</h2>
			<a href="signup.php" class="button">注册</a><br /><br />
			<a href="build-logs.php" class="button">日志</a><br /><br />
			<a href="viewer/prophet.php" class="button">旁观</a>
		</div>
	</div>
	</center>
	<footer>
		本站可能在低级浏览器下布局异常(如KCV内置的IE)
	</footer>
	<script type="text/javascript">
		function login() {
			var user = document.getElementById('username').value;
			var pswd = document.getElementById('password').value;
			var shaObj = new jsSHA("SHA-512", "TEXT");
			shaObj.update(pswd);
			pswd = shaObj.getHash("HEX");
			$.cookie("username", user, { expires: 60, path: '/' });
			$.cookie("passhash", pswd, { expires: 60, path: '/' });
			window.location.assign("/");
		}

	<?php if ($_REQUEST["failure"]==="auth"): ?>
	// Reset saved credentials
	$.removeCookie("username");
	$.removeCookie("passhash");
	alert("无效的用户名密码");
	<?php endif; ?>
	</script>
</body>
</html>
