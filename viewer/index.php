<?php
//header("Cache-Control: max-age=86400");
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Viewer</title>
	<script type="text/javascript" src="bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="bower_components/react/react.js"></script>
	<script type="text/javascript" src="bower_components/react/react-dom.js"></script>
	<script type="text/javascript" src="bower_components/react-bootstrap/react-bootstrap.js"></script>
	<link rel="stylesheet" type="text/css" href="assets/themes/paperdark/css/paperdark.css" />
	<script type="text/javascript" src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="bower_components/font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="underscore-min.js"></script>
	<script type="text/javascript" src="fontawesome.js"></script>
	<script type="text/javascript" src="bower_components/mustache.js/mustache.min.js"></script>
	<link rel="stylesheet" type="text/css" href="assets/css/app.css" />

	<script type="text/javascript" src="poi.js"></script>
	<script type="text/javascript" src="modules/prophet.js"></script>
	<script type="text/javascript" src="modules/expedition.js"></script>
	<script type="text/javascript" src="modules/expcalc.js"></script>
	<script type="text/javascript" src="modules/questinfo.js"></script>
	<script type="text/javascript" src="modules/itemimprov.js"></script>
	<script type="text/javascript" src="modules/shipinfo.js"></script>
	<script type="text/javascript" src="modules/iteminfo.js"></script>
	<script type="text/javascript" src="modules/dashboard.js"></script>

	<link rel="stylesheet" type="text/css" href="assets/css/shipinfo.css" />
	<link rel="stylesheet" type="text/css" href="assets/css/iteminfo.css" />

	<script type="text/javascript">
	window.notify = function(msg) {
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
		$owner = false;
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
						$owner = false;
						echo 'window.user='.$user->id.";\n";
					} else {
						echo 'alert("授权证书已过期");';
					}
				}
			}
		}

		if (!$init) {
			if (isset($_COOKIE['username']) && isset($_COOKIE['passhash'])) {
				if ($user->initWithAuth($_COOKIE['username'], $_COOKIE['passhash'])) {
					echo 'window.user='.$user->id.";\n";
					echo "window.token='".$user->token."';\n";
					$owner = true;
				} else {
					echo 'alert("验证失败,请重新登录.");';
				}
			}
		};

	?>

	var start2 = {};
	var checksum = "";
	var polling = true;
	window.connected = true;
	var updateData = function() {
		setTimeout(updateData, 1000);
		if (polling) {
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
						connected = true;
						notify("连接已恢复.");
					};
					if (data.status==200) {
						urlparts = data.detail.path.split("/");
						notify("获得数据:"+urlparts[urlparts.length-1]+"("+data.checksum.substr(0,8)+")");
						console.log("Data update: "+data.checksum);
						checksum = data.checksum;
						if (data.detail.path == "/kcsapi/api_port/port" && data.detail.body.api_slot_item) {
							// Integrated port: parse slotitem data first
							poi.resolve({detail:{
								path: "/kcsapi/api_get_member/slot_item",
								method: "POST",
								postBody: {},
								body: data.detail.body.api_slot_item
							}})
						};
						poi.resolve(data);
					}
				}
			});
		}
	}
	var initialPort = function() {
		notify("初始化Port...");
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
				console.error(e);
			},
			success: function(data) {
				poi.resolve(data);
				window.dispatchEvent(new CustomEvent("game.response", data));
				updateData();
			}
		})
	};
	var initData = function() {
		notify("初始化游戏数据...");
		$.ajax({
			url: "prophet-fetch.php",
			method: "POST",
			data: {
				user: user,
				action: "init"
			},
			dataType: "json",
			error: function(e) {
				displayError("无法初始化游戏数据");
				console.error(e);
			},
			success: function(e) {
				for (var idx in e.seq) {
					poi.resolve({detail:e.seq[idx]});
				}
				initialPort();
			}
		})
	};
	var prophet;
	</script>
</head>
<body>
<script type="text/javascript">
	var pages = {
		current: "MainView",
		open: function(page) {
			document.getElementById(this.current).style.display="none";
			document.getElementById(page).style.display="block";
			this.current = page;
		}
	}
	function togglePolling(caller) {
		window.polling = !window.polling;
		if (window.polling) {
			caller.className = "btn btn-success";
		} else {
			caller.className = "btn btn-danger";
		}
	}
	function toggleInspector(caller) {
		var modules = document.querySelector("#modules");
		if (modules.style.display == "block") {
			modules.style.display = "none";
			caller.className = "btn btn-danger";
		} else {
			modules.style.display = "block";
			caller.className = "btn btn-success";
		}
	}
</script>
<div style="padding:0px;margin:0px;display:flex;height:37px;overflow:hidden" id="topbar">
	<button class="btn btn-default btn-sm" style="vertical-align:top" onclick="selectUser();" href="#" >切换用户</button>
	<span style="display:inline-block; font-family: monospace, 'Courier New'; height:2em; vertical-align:bottom; overflow:hidden; word-break: keep-all;flex-grow:1" id="notification">加载组件...</span>
	<select class='form-control' onchange="pages.open(this.value)" style="width:5.5em">
		<option value='MainView'>概览</option>
		<option value='prophet'>战斗分析</option>
		<option value='expcalc'>经验计算</option>
		<option value='expedition'>远征信息</option>
		<option value='quest-info'>任务信息</option>
		<option value='quest'>任务</option>
		<option value='item-improv'>装备改修</option>
		<option value='ship-info'>船只信息</option>
		<option value='item-info'>装备信息</option>
	</select>
	<button class="btn btn-success" onclick="togglePolling(this)" title="闲置时请关闭监听">监听</button>
	<button class="btn btn-success" onclick="toggleInspector(this)">显示</button>
</div>


<div id="modules" style="z-index:1;background-color: rgba(41,41,41,0.8);overflow:auto;display:block"> <!-- Modules Area -->
	<div style="display:block" id="MainView"></div>
	<div style="display:none" id="prophet"></div>
	<div style="display:none" id="expedition"></div>
	<div style="display:none" id="expcalc"></div>
	<div style="display:none" id="quest-info"></div>
	<div style="display:none" id="quest"><?php include "quest.html";?></div>
	<div style="display:none" id="item-improv"></div>
	<div style="display:none" id="ship-info"></div>
	<div style="display:none" id="item-info"></div>
</div>

<?php
if (isset($owner) && $owner === true && !isset($_REQUEST["standalone"])) {
?>
<iframe src="/p" seamless="seamless" scrolling="no" style="width:100%;height:100%;z-index:-10;border:0px;position:absolute;top:37px;overflow:hidden" id="game"></iframe>
<?php
}
?>

<script type="text/javascript">
function resize() {
	document.querySelector("#modules").style.height = (window.innerHeight-37)+"px";
	if (document.querySelector("#game")) {
		document.querySelector("#game").style.height = (window.innerHeight-37)+"px";
	};
}
window.onresize = resize;
resize();
</script>

<script type="text/javascript">
	try {
		dashboard = React.createElement(MainView.reactClass);
		ReactDOM.render(dashboard, document.getElementById('MainView'));
	} catch(e) {
		console.error(e);
		console.error("dashboard failed to load");
	}

	try {
		prophet = React.createElement(Prophet.reactClass);
		ReactDOM.render(prophet, document.getElementById('prophet'));
	} catch(e) {
		console.error(e);
		console.error("prophet failed to load");
	}

	try {
		expedition = React.createElement(Expedition.reactClass);
		ReactDOM.render(expedition, document.getElementById('expedition'));
	} catch(e) {
		console.error(e);
		console.error("expedition failed to load");
	}

	try {
		expcalc = React.createElement(ExpCalc.reactClass);
		ReactDOM.render(expcalc, document.getElementById('expcalc'));
	} catch(e) {
		console.error(e);
		console.error("expcalc failed to load");
	}

	try {
		questinfo = React.createElement(QuestInfo.reactClass);
		ReactDOM.render(questinfo, document.getElementById('quest-info'));
	} catch(e) {
		console.error(e);
		console.error("questinfo failed to load");
	}

	try {
		itemimprov = React.createElement(ItemImprovArea);
		ReactDOM.render(itemimprov, document.getElementById('item-improv'));
	} catch(e) {
		console.error(e);
		console.error("itemimprov failed to load");
	}

	try {
		shipinfo = React.createElement(ShipInfoArea);
		ReactDOM.render(shipinfo, document.getElementById('ship-info'));
	} catch(e) {
		console.error(e);
		console.error("shipinfo failed to load");
	}

	try {
		iteminfo = React.createElement(ItemInfoArea);
		ReactDOM.render(iteminfo, document.getElementById('item-info'));
	} catch(e) {
		console.error(e);
		console.error("iteminfo failed to load");
	}
</script>

<script type="text/javascript" src="modules/init.js?user=<?php echo $user->id; ?>"></script>

</body>
</html>

