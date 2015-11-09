<?php
$flashurl="/kcs/mainD2.swf?api_token=$_REQUEST[token]&api_starttime=$_REQUEST[starttime]";
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title>舰队Collection - NFLS.GA</title>
	<style type="text/css">
	body {
		background: black;
		color: #666;
		padding: 0px;
		margin: 0px;
	}
	iframe {
		border: none;
		padding: 0px;
		margin: 0px;
	}

	</style>
	<script type="text/javascript">
	var games = [null];
	var page_idx = 1;

	var resizeTable = function() {
		var width = window.innerWidth;
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
		}
		var idx = parseInt(chr);
		if (!isNaN(idx) && idx) {
			switchPage(idx);
		}
	}

	window.onresize = resizeTable;
	window.onkeypress = keyPressed;
	</script>
</head>
<body>
	<div>
		<div id="gamearea" align="center"></div>
		<div id="naviarea" align="center" style="font-family:monospace"></div>
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
