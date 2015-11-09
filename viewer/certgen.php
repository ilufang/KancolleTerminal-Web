<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>创建直播用证书</title>
</head>
<body>
	<?php if ($_SERVER['HTTPS'] !== 'on'){ ?>
	<a href='https://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>' style="color:red">建议使用HTTPS连接</a><br />
	<?php } ?>

	<?php if (!isset($_REQUEST['user'])){?>
	<form method="POST">
	<label>用户名:</label><input type="text" name="user" <?php
	if (isset($_COOKIE['username'])) {
		echo "value='$_COOKIE[username]'";
	}
	?> /><br />
	<label>密码:</label><input type="password" name="pswd" <?php
	if (isset($_COOKIE['passhash'])) {
		echo "value='$_COOKIE[passhash]'";
	}
	?> /><br />
	<label>有效期:</label>
	<input type="number" name="exp_d" value="0" />日
	<input type="number" name="exp_h" value="1" />小时
	<input type="number" name="exp_m" value="0" />分钟

	<hr />
	<input type="submit" value="创建" />
	</form>
	<?php } else { ?>
	<a href="certgen.php" target="_self">返回</a><hr />
	<?php
		// Process
		require_once '../KCUser.class.php';
		$user = new KCUser();
		if (!$user->initWithAuth($_REQUEST['user'], $_REQUEST['pswd'])) {
		?>
			<strong style='color:red'>用户名密码验证失败.</strong>
		<?php
		} else {
			// Auth successful
			$day = intval($_REQUEST['exp_d']);
			$hrs = intval($_REQUEST['exp_h']) + $day*24;
			$min = intval($_REQUEST['exp_m']) + $hrs*60;
			// Encrypt
			$key = md5($user->password);
			$key_size = strlen($key);
			$cert = array(
				't'=>time(),
				'l'=>$min*60
			);
		    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		    $cert_secret = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, json_encode($cert), MCRYPT_MODE_CBC, $iv);
		    $cert_plain = array(
				'u'=>$user->name,
				'd'=>base64_encode($iv.$cert_secret)
			);
			?>
			<strong>证书创建成功,请复制以下代码分享</strong>
			<style type="text/css">
			.code {
				background: #eee;
				font-family: monospace, "Courier New";
				word-break: break-all;
				padding: 1em;
			}
			</style>
			<div class='code'><?php echo base64_encode(json_encode($cert_plain));?></div>
			或者复制链接(右键复制链接地址):
			<ul>
				<li><a href='prophet.php?cert=<?php echo base64_encode(json_encode($cert_plain));?>'>prophet</a></li>
			</ul>
			<?php
		}
	} ?>
</body>
</html>
