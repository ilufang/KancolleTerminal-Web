<?php
/**
 *	home
 *
 *	User's homepage
 *	Also does authentication
 *
 *	2015 by ilufang
 */

require_once 'config.php';

$protocol = "unset";

if (isset($_COOKIE['ssl'])) {
	$protocol = $_COOKIE['ssl'];
}

$active_protocol = "http";
if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
	$active_protocol = "https";
}

if ($protocol==="http" && $active_protocol !== "http") {
	header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	die();
}

if ($protocol==="https" && $active_protocol !== "https") {
	header('Location: https://'.$config['serveraddr'].$_SERVER['REQUEST_URI']);
	die();
}

// Authenticate first
require_once 'KCUser.class.php';

if (!isset($_COOKIE["username"])) {
	header("Location: login.php");
	die();
}

$user = new KCUser();
if (!$user->initWithAuth($_COOKIE["username"],$_COOKIE["passhash"])) {
	header("Location: login.php?failure=auth");
	die();
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Kancolle Terminal</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript">
	var user = {
		username: '<?php echo $_COOKIE["username"]?>',
		passhash: '<?php echo $_COOKIE["passhash"]?>',
		token: '<?php echo $user->token;?>'
	};
	$.cookie("username", user.username, { expires: 60, path: '/' });
	$.cookie("passhash", user.passhash, { expires: 60, path: '/' });
	function logout() {
		$.removeCookie("username");
		$.removeCookie("passhash");
		window.location.assign("login.php");
	}
	</script>
</head>
<body>
<div class="toolbar">
	<h1><?=$config['title']?></h1>
	<div>
		<?php echo $_COOKIE["username"];?>&nbsp;|&nbsp;
		<a href="ssl.php">切换HTTS/HTTP</a>&nbsp;|&nbsp;
		<a href="javascript:logout()">退出</a>

	</div>

</div>
<br><br>
<?php
	switch ($user->gamemode) {
		case 0:
		case 1:
		case 2:
			echo "Not Yet Implemented";
			break;
		case 3:
			include 'forwardhome.php';
			break;
	}
?>
<footer>
本站可能在低级浏览器下布局异常(如KCV内置的IE)
</footer>
</body>
</html>
