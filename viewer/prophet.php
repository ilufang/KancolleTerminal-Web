<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Prophet</title>
	<script type="text/javascript" src="bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="bower_components/react/react.js"></script>
	<script type="text/javascript" src="bower_components/react/react-dom.js"></script>
	<script type="text/javascript" src="bower_components/react-bootstrap/react-bootstrap.js"></script>
	<link rel="stylesheet" type="text/css" href="bower_components/themes/paper-dark.css" />
	<link rel="stylesheet" type="text/css" href="bower_components/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="prophet.css" />
	<link rel="stylesheet" type="text/css" href="poi.css" />

	<script type="text/javascript" src="poi.js"></script>
	<script type="text/javascript" src="prophet.js.php"></script>

	<script type="text/javascript">
	var notify = function(msg) {
		document.getElementById('topbar').style.background="none";
		document.getElementById('notification').innerHTML = msg;
	}

	window.error = function(msg) {
		document.getElementById('topbar').style.background="#ff6666";
		document.getElementById('notification').innerHTML = msg;
	}

	var selectUser = function() {
		var username = prompt("请粘贴授权证书");
		if (username) {
			window.location.assign("prophet.php?cert="+username);
		}
	}

	var user = false;
	<?php
		require_once '../KCUser.class.php';
		$user = new KCUser();
		$init = false;
		if (isset($_REQUEST['select'])) {
			$init = true;
			echo 'selectUser();';
		} elseif (isset($_REQUEST['cert']) && $_REQUEST['cert']) {
			// Unpack, decode
			$keydata = json_decode(base64_decode($_REQUEST['cert']),true);
			if (!$user->initWithUsername($keydata['u'])) {
				echo 'alert("授权证书无效(未知来源)");';
			} else {
				$key = md5($user->password);
				$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
				$secretData = base64_decode($keydata['d']);
				$iv_dec = substr($secretData, 0, $iv_size);
				$text_dec = substr($secretData, $iv_size);
				echo "//".mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text_dec, MCRYPT_MODE_CBC, $iv_dec)."\n";
				$certData = json_decode(trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $text_dec, MCRYPT_MODE_CBC, $iv_dec)), true);
				if (!$certData) {
					echo 'alert("授权证书无效");';
				} else {
					$time = time();
					if ($time > $certData['t'] && $time - $certData['t'] < $certData['l']) {
						$init = true;
						echo 'user='.$user->id.';';
					} else {
						echo 'alert("授权证书已过期");';
					}
				}
			}
		}

		if (!$init) {
			if (isset($_COOKIE['username']) && isset($_COOKIE['passhash'])) {
				if ($user->initWithAuth($_COOKIE['username'], $_COOKIE['passhash'])) {
					echo 'user='.$user->id.';';
				} else {
					echo 'alert("验证失败,请重新登录.");';
				}
			}
		};

	?>

	var start2 = {};
	var checksum = "";
	var connected = true;
	var updateData = function() {
		setTimeout(updateData, 1000);
		$.ajax({
			url: "prophet-fetch.php",
			method: "POST",
			data: {
				checksum: checksum,
				user: user
			},
			dataType: "json",
			error: function(e) {
				connected = false;
				displayError("无法连接到服务器.");
				//console.log("Network disconnected");
			},
			success: function(data) {
				//console.log(data);
				if (!connected) {
					notify("连接已恢复.");
				};
				if (data.status==200) {
					urlparts = data.detail.path.split("/");
					notify("获得数据:"+urlparts[urlparts.length-1]+"("+data.checksum.substr(0,8)+")");
					console.log("Data update: "+data.checksum);
					checksum = data.checksum;
					poi.handleResponse(data);
					window.dispatchEvent(new CustomEvent("game.response", data));
				}
			}
		})
	}
	var initData = function() {
		$.ajax({
			url: "prophet-fetch.php",
			method: "POST",
			data: {
				user: user,
				action: "port"
			},
			dataType: "json",
			error: function(e) {
				displayError("无法加载Port.请在游戏中回港");
				console.log(e);
			},
			success: function(data) {
				poi.handleResponse(data);
				window.dispatchEvent(new CustomEvent("game.response", data));
				updateData();
			}
		})
	}
	var prophet;
	</script>
</head>
<body>
<div style="padding:0px;margin:0px" id="topbar">
<button class="btn btn-default btn-sm" style="vertical-align:top" onclick="selectUser();" href="#" >切换用户</button>
<span style="display:inline-block; font-family: monospace, 'Courier New'; height:2em; vertical-align:bottom; overflow:hidden; word-break: keep-all" id="notification"></span>
</div>
<div id="prophet">
</div>
<script type="text/javascript">
if (user) {
	notify("正在加载...");
	$.ajax({
		url: "prophet-fetch.php",
		method: "POST",
		data: {
			user: user,
			action: "start2"
		},
		dataType: "json",
		error: function(e) {
			displayError("无法加载Start2.请刷新游戏");
			console.log(e);
		},
		success: function(data) {
			poi.handleResponse(data);
			prophet = React.createElement(Prophet.reactClass);
			ReactDOM.render(prophet, document.getElementById('prophet'));
			initData();
		}
	});
} else {
	displayError("请登录");
}

</script>
</body>
</html>

