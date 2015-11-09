<?php
/**
 *	signup
 *
 *	Create new users
 *	Display page & process request
 *
 *	2015 by ilufang
 */

function randToken() {
	$token = "";
	$charset = "0123456789abcdef";
	for ($i=0; $i<40; $i++) {
		$idx = rand(0,15);
		$token .= substr($charset, $idx, 1);
	}
	return $token;
}

$errmsg = "";

if ($_REQUEST["eula"]==="accept") {
	$user = $_REQUEST["username"];
	$pswd = $_REQUEST["password"];
	$gamemode = intval($_REQUEST["gamemode"]);
	if (strlen($pswd)!=128) {
		$errmsg = "脚本错误. 上传密码时未经加密.";
	} else {
		if (isset($user)&&isset($pswd)&&isset($gamemode)) {
			require_once "KCSql.class.php";
			if (KCSql::inst()->selectAll("hub_users")->where("username='$user'")->query()!==true) {
				$errmsg = "用户名已被注册";
			} else {
				if(KCSql::inst()->insert(array("username"=>$user,"password"=>sha1($pswd),"gamemode"=>$gamemode,"token"=>randToken()),"hub_users")->query()) {
					$memberid = KCSql::inst()->insertId();
					switch ($gamemode) {
						case 0:
						case 1:
						case 2:
							$errmsg = "W.I.P.";
							break;
						case 3:
							if(KCSql::inst()->insert(array("memberid"=>$memberid,"lastupdate"=>"Never(未定义)"),"forward_users")->query()) {
								header("Location: home.php?user=$user&pswd=$pswd");
								die();
							} else {
								$errmsg = "数据库请求错误: ".KCSql::inst()->error();
							}
							break;
					}
				} else {
					$errmsg = "数据库请求错误: ".KCSql::inst()->error();
				}
			}
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>Kancolle Terminal Signup</title>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
	<script type="text/javascript" src="js/sha.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script type="text/javascript">
		function signup() {
			var user = document.getElementById('username').value;
			var pswd = document.getElementById('password').value;
			var shaObj = new jsSHA("SHA-512", "TEXT");
			shaObj.update(pswd);
			pswd = shaObj.getHash("HEX");
			if (document.getElementById("savecreds").checked) {
				$.cookie("username", user, { expires: 60, path: '/' });
				$.cookie("passhash", pswd, { expires: 60, path: '/' });
			}
			document.getElementById('password').value = pswd;
			return true;
		}
	</script>

</head>
<body>
	<h1>Kancolle Terminal</h1>
	<div class="box" style="width:80%">
		<form method="POST" onsubmit="signup()">
			<h2>用户注册</h2>
			<span style="color:red"><?php echo $errmsg;?></span><br />
			<input type="text" name="username" placeholder="用户名" id="username" required value="<?php if (isset($_REQUEST["username"]))echo $_REQUEST["username"]; ?>"/><br />
			<input type="password" name="password" id="password" placeholder="密码" required /><br />
			游戏模式:
			<select name="gamemode">
				<option value="3">转发</option>
			</select>
			<!--p>
				<b>关于游戏模式</b><br/>
				(对,gamemode是仿Minecraft的,不用在意)
				<ol start="0">
					<li>生存: W.I.P.</li>
					<li>创造: W.I.P.</li>
					<li>冒险: W.I.P.</li>
					<li>观察者/转发: 作为代理和缓存加速, 透过本站访问你原来的服务器</li>
				</ol>
			</p>
			<div style="height:8em;overflow:auto;border: 1px dashed #ccc;"><?php
			echo file_get_contents("eula.html");
			?></div-->
			<input type="hidden" name="eula" required value="accept"><!--label>我已阅读,了解并同意上述条款</label--><br />
			<input type="checkbox" id="savecreds"><label>自动登录</label><br />
			<input type="submit" value="注册"/>
		</form>
	</div>
	<footer>
		本站可能在低级浏览器下布局异常(如KCV内置的IE)
	</footer>
</body>
</html>
