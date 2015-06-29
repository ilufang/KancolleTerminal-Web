<?php
/**
 *	home
 *
 *	User's homepage
 *	Also does authentication
 *
 *	2015 by ilufang
 */

// Authenticate first
require_once 'KCUser.class.php';

$user = new KCUser();
if (!$user->initWithAuth($_REQUEST["user"],$_REQUEST["pswd"])) {
	header("Location: index.php?action=flush");
	die();
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>Kancolle Terminal</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript">
	var user = {
		username: '<?php echo $_REQUEST["user"]?>',
		passhash: '<?php echo $_REQUEST["pswd"]?>'
	};
	function logout() {
		$.removeCookie("username");
		$.removeCookie("passhash");
		window.location.assign("index.php");
	}
	</script>
</head>
<body>
<h1>Kancolle Terminal</h1>
<div class="box" style="width:80%">
	<div style="text-align:right">
		<?php echo $_REQUEST["user"]?> | 
		<a href="javascript:logout()">退出</a>
	</div>
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
</div>
<footer>
本站可能在低级浏览器下布局异常(如KCV内置的IE)
</footer>
</body>
</html>