<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>禁言管理器</title>
</head>
<body>
需要三张有效(未过期)票来封禁用户.
<hr>
<?php
require_once 'KCBan.class.php';
$ban = new KCBan("../ban.json");
$banlist = $ban->banlist();
$timerIdx = 0;
$timerList = array();
foreach ($banlist as $bannedUser => $voteList) {
	echo "<h2 id='user_$bannedUser'>$bannedUser</h2>\n";
	echo "<ul>\n";
	foreach ($voteList as $voteip => $remainingtime) {
		echo "<li><strong><code>$voteip</code></strong>: <code id='bantimer_$timerIdx'></code></li>\n";
		$timerIdx++;
		$timerList[] = $remainingtime;
	}
	echo "</ul><hr>\n";
}
?>
<script type="text/javascript">
var banTime = <?php echo json_encode($timerList);?>;
function timeExpr(seconds) {
	if (seconds>12*60*60) seconds=12*60*60;
	var hrs = Math.floor(seconds / 3600);
	seconds %= 3600;
	var mins = Math.floor(seconds / 60);
	seconds %= 60;
	var expr = hrs+":";
	if (mins<10) {
		expr+="0";
	}
	expr+=mins+":";
	if (seconds<10) {
		expr+="0";
	}
	expr+=seconds;
	return expr;
}
function updateTime() {
	setTimeout(updateTime, 1000);
	for (var i = 0; i < banTime.length; i++) {
		banTime[i]--;
		if (banTime[i]<0) {
			banTime[i] = 0;
		};
		document.getElementById('bantimer_'+i).innerHTML = timeExpr(banTime[i]);
	};
}
updateTime();
</script>

<form method="POST" action="ban.php">
	封禁用户名: <input type="text" name="user" /><br />
	<input type="submit" value="投票" />
</form>

<a href="/kcapi/dmm-names.json" target="_blank">活跃用户名查询</a>
<a href="/build-logs.php" target="_blank">欧洲日志</a>

</body>
</html>
