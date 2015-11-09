<?php
if (!isset($_REQUEST['user'])) {
	header("Location: index.php");
	die();
}

$user = $_REQUEST['user'];

require_once 'KCBan.class.php';

$ban = new KCBan("../ban.json");

$iphash = $ban->vote($user);
	header("Location: index.php");
	die();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Vote Succeeded</title>
</head>
<body>
<code>
	Vote on <?php echo $user;?> succeeded.<br />
	Your IP is: <?php echo $_SERVER['REMOTE_ADDR'];?><br />
	You will be displayed as <?php echo $iphash;?>.
</code>
<hr>
<a href="index.php">Back</a>
</body>
</html>

