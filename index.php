<?php
/**
 *	index
 *
 *	Oh, index.
 *	Entry point
 *
 *	2015 by ilufang
 */

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>Kancolle Terminal</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/sha.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
	<h1>Kancolle Terminal</h1>
	<center>
		<div class="box" style="width:12em">
			<form onsubmit="login();return false;">
			<h2>Login</h2>
				<input type="text" id="username" placeholder="用户名" required /><br />
				<input type="password" id="password" placeholder="密码" required /><br />
				<input type="checkbox" id="savecreds"><label>下次自动登录</label><br />
				<input type="submit" value="Login"/>
			</form>
		</div>
		<div class="box" style="width:12em">
			<h2>Signup</h2>
			<p>
				我们将会为你创建一个账号来记录你的信息, 以便下次使用时可以更快地打开.
			</p>
			<input type="button" onclick="signup()" value="Sign Up"/>
		</div>
		<div style="display:none">
			<form id="redir" method="POST" action="home.php">
				<input type="hidden" name="user" id="jmp_user" />
				<input type="hidden" name="pswd" id="jmp_pswd" />
			</form>
		</div>
	</center>
	<footer>
		本站可能在低级浏览器下布局异常(如KCV内置的IE)
	</footer>
	<script type="text/javascript">
		function jump(user, pswd) {
			document.getElementById('jmp_user').value = user;
			document.getElementById('jmp_pswd').value = pswd;
			document.getElementById('redir').submit();
		}

		function login() {
			var user = document.getElementById('username').value;
			var pswd = document.getElementById('password').value;
			var shaObj = new jsSHA("SHA-512", "TEXT");
			shaObj.update(pswd);
			pswd = shaObj.getHash("HEX");
			if (document.getElementById("savecreds").checked) {
				$.cookie("username", user, { expires: 60, path: '/' });
				$.cookie("passhash", pswd, { expires: 60, path: '/' });
			}
			jump(user,pswd);
		}

		function signup() {
			window.location.assign("signup.php");
		}

	</script>
	<script type="text/javascript">

	<?php if ($_REQUEST["action"]==="flush"): ?>
	// Reset saved credentials
	$.removeCookie("username");
	$.removeCookie("passhash");
	alert("无效的用户名密码");
	<?php endif; ?>

	// Autologin script
	var user = $.cookie("username");
	var pswd = $.cookie("passhash");
	if (user && pswd) {
		jump(user, pswd);
	};
	</script>
</body>
</html>
