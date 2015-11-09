<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>舰队Collection - NFLS.GA</title>
	<!-- prophet injection -->
	<script type="text/javascript" src="viewer/bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="viewer/bower_components/react/react.js"></script>
	<script type="text/javascript" src="viewer/bower_components/react/react-dom.js"></script>
	<script type="text/javascript" src="viewer/bower_components/react-bootstrap/react-bootstrap.js"></script>
	<link rel="stylesheet" type="text/css" href="viewer/bower_components/themes/paper-dark.css" />
	<link rel="stylesheet" type="text/css" href="viewer/bower_components/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="viewer/prophet.css" />
	<link rel="stylesheet" type="text/css" href="viewer/poi.css" />

	<script type="text/javascript" src="viewer/poi.js"></script>
	<script type="text/javascript" src="viewer/prophet.js.php"></script>

	<script type="text/javascript">
	var notify = function(msg) {
		document.getElementById('notification').style.background="none";
		document.getElementById('notification').innerHTML = msg;
	}

	var displayError = function(msg) {
		document.getElementById('notification').style.background="#f66";
		document.getElementById('notification').innerHTML = msg;
	}

	<?php
		require_once 'KCUser.class.php';
		$user = new KCUser();
		if ($user->initWithToken($_REQUEST['token'])) {
			echo "user=".$user->id.";";
		} else {
			echo "displayError('Token无效');";
		}
	?>

	var start2 = {};
	var checksum = "";
	var updateData = function() {
		setTimeout(updateData, 1000);
		$.ajax({
			url: "viewer/prophet-fetch.php",
			method: "POST",
			data: {
				checksum: checksum,
				user: user
			},
			dataType: "json",
			error: function(e) {
				displayError("无法连接到服务器.");
				//console.log("Network disconnected");
			},
			success: function(data) {
				//console.log(data);
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
			url: "viewer/prophet-fetch.php",
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
	<!-- End prophet -->

	<style type="text/css">
	body, embed, div {
		padding: 0px;
		margin: 0px;
	}
	#overlay {
		z-index: 2;
		position: absolute;
		left: 0px;
		top: 0px;
		opacity: 0.1;
		background: #ccc;
	}
	#prophet {
		display: inline-block;
		z-index: 3;
		position: absolute;
		left: 0px;
		top: 0px;
		background: rgba(0,0,0,0.8);
		display: none;
		overflow: auto;
	}
	#viewerbg {
		background: rgba(255,255,255,0.4);
		width: 100%;
		height: 100%;
		position: absolute;
		left: 0px;
		right: 0px;
		top: 0px;
		bottom: 0px;
		display: none;
	}
	#notification {
		padding: 0.3em;
		margin: 0px;
		font-family: monospace, "Courier New";
		text-align: center;
	}
	</style>

</head>
<body>
<?php
$flashurl="/kcs/mainD2.swf?api_token=$_REQUEST[token]&api_starttime=$_REQUEST[starttime]";
?>
<div>
	<embed src="<?php echo $flashurl;?>" type="application/x-shockwave-flash" width="800" height="480" base="/kcs/" style="z-index:1"/>
	<div id="notification"></div>
</div>
<div id="overlay" onclick="showInspector()"></div>

<script type="text/javascript">

var resize = function() {
	var width = window.innerWidth;
	var height = width / 800 * 480;
	document.querySelector("embed").width=width;
	document.querySelector("embed").height=height;
	document.querySelector("#overlay").style.width = Math.floor(width/8)+"px";
	document.querySelector("#overlay").style.height = Math.floor(height/8)+"px";
	document.querySelector("#prophet").style.height = height+"px";
	document.querySelector("#prophet").style.width = Math.floor(width*0.6)+"px";
}

function showInspector() {
	document.querySelector("#viewerbg").style.display = "block";
	document.querySelector("#prophet").style.display = "block";
}
function hideInspector() {
	document.querySelector("#viewerbg").style.display = "none";
	document.querySelector("#prophet").style.display = "none";
	resize();
}

window.onresize = resize;
resize();
</script>


<div id="viewerbg" onclick="hideInspector()">
	<div id="prophet"></div>
</div>


<script type="text/javascript">
if (user) {
	notify("正在加载...");
	$.ajax({
		url: "viewer/prophet-fetch.php",
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
			resize();
			initData();
		}
	});
} else {
	displayError("未指定用户");
}

</script>

</body>
</html>
