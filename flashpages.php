<?
/**
 *	flash pages
 *
 *	Simultaneously run up to 10 instances of the game in a window
 *
 *	2015 by ilufang
 */
require_once 'config.php';
?><?php
require_once 'config.php';
$flashurl=$_SERVER['QUERY_STRING'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title><?=$config['title']?></title>
	<style type="text/css">
	body {
		background: black;
		color: #666;
		padding: 0px;
		margin: 0px;
	}
	iframe, div {
		border: 0px;
		padding: 0px;
		margin: 0px;
	}
	#naviarea {
		font-family: monospace, "Courier New";
		transition: background 0.3s, color 0.3s;
	}

	</style>
	<script type="text/javascript">
	var games = [null];
	var page_idx = 1;

	var resizeTable = function() {
		var width = window.innerWidth;
		console.log(JSON.stringify({evt:"resize",width:width}));
		var height = width / 800 * 480;
		for (var i=0; i<games.length; i++) {
			if (games[i]) {
				games[i].style.width = width+"px";
				games[i].style.height = height+"px";
			}
		}
	}

	var current_page = 1;

	var switchPage = function(idx, cycle) {
		if (cycle) {
			var idx_t = page_idx + idx;
			if (idx_t > 9) idx_t = 1;
			if (idx_t < 1) idx_t = 9;
			while(!games[idx_t] || games[idx_t].src=="about:blank") {
				idx_t += idx;
				if (idx_t > 9) idx_t = 1;
				if (idx_t < 1) idx_t = 9;
			}
			idx = idx_t;
		}

		if (document.getElementById('game_'+page_idx)) {
			document.getElementById('game_'+page_idx).style.display="none";
		}
		page_idx = idx;
		if (!games[idx]) {
			var gamepage = document.createElement("iframe");
			gamepage.src="<?php echo $flashurl;?>";
			games[idx] = gamepage;
			var container = document.createElement("div");
			container.id="game_"+idx;
			container.appendChild(gamepage);
			document.getElementById('gamearea').appendChild(container);
			resizeTable();
			setTimeout(function() {
				games[idx].contentDocument.body.onkeypress = keyPressed;
			}, 500);
		}
		if (games[idx].src=="about:blank") {
			games[idx].src="<?php echo $flashurl;?>";
		}
		current_page = idx;
		document.getElementById('game_'+page_idx).style.display="block";
		var navbar = "";
		for (var i=1; i<=9; i++) {
			if (!games[i] || games[i].src=="about:blank") {
				navbar += ".";
			} else if (i==idx) {
				navbar += idx;
			} else {
				navbar += "-";
			}
		}
		document.getElementById('naviarea').innerHTML = navbar;
	}

	var keyPressed = function(e) {
		var chr = String.fromCharCode(e.keyCode);
			console.log(e);
		if (chr == '`') {
			switchPage(1, true);
			return;
		}
		if (chr == '~') {
			switchPage(-1, true);
			return;
		}
		if (chr == 'd' && current_page != 1 && confirm("Delete current page?")) {
			// Delete entry
			games[current_page].src="about:blank";
			switchPage(-1, true);
		}
		if (chr == 'r' && confirm("Reset current page?")) {
			// Reset page
			games[current_page].src += "";
			setTimeout(function() {
				games[current_page].contentDocument.body.onkeypress = keyPressed;
			}, 500);

		}
		var idx = parseInt(chr);
		if (!isNaN(idx) && idx) {
			switchPage(idx);
		}
	}

	window.onresize = resizeTable;
	window.onkeypress = keyPressed;

/*
	function triggerLocating(display) {
		if (display) {
			var color = "hsl("+Math.floor(Math.random()*360)+",100%,70%)"
			document.getElementById("naviarea").style.background = color;
			document.getElementById("naviarea").style.color = color;
		} else {
			document.getElementById("naviarea").style.background = "none";
			document.getElementById("naviarea").style.color = "#666";
		}
	}
*/

	// kctermjs trigger
	function refreshGame() {
		games[current_page].src += "";
		setTimeout(function() {
			games[current_page].contentDocument.body.onkeypress = keyPressed;
		}, 500);
	}
	</script>
</head>
<body>
	<div>
		<div id="gamearea" align="center"></div>
		<!--<div id="naviarea" align="center" onmouseover="triggerLocating(true)" onmouseout="triggerLocating(false)"></div>-->
		<div id="naviarea" align="center"></div>
		<div align="center">
			键盘1-9: 切换页面/打开新页面<br />
			`,Shift+`: 在已打开页面中循环切换<br />
			d: 删除当前页面(释放内存,第一页不能删除)<br />
			r: 重载当前页面
		</div>
		<script type="text/javascript">
		switchPage(1);
		</script>
	</div>
</body>
</html>
