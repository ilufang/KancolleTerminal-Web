<?php
require_once 'KCSql.class.php';
$db = json_decode(file_get_contents("kcapi/gamedb.json"),true);
$users = json_decode(file_get_contents("kcapi/dmm-names.json"),true);
?>
<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>欧洲日志: 非洲血泪史</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<style type="text/css">
	table {
		border: 1px solid #666;
		border-collapse: collapse;
		font-family: monospace;
	}
	tr {
		border:1px dotted #ccc;
	}

	.shiprarity_1, .shiprarity_2, .shiprarity_3, .itemrarity_0, .itemrarity_ {
		color: #999;
	}
	.shiprarity_4, .shiprarity_5, .itemrarity_1 {
		color: black;
	}
	.shiprarity_6, .shiprarity_7, .itemrarity_2 {
		color: red;
	}
	.shiprarity_8, .itemrarity_3 {
		color: red;
		font-weight: bold;
	}

	td {
		text-align: center;
	}
	code {
		display: inline-block;
		background: #eee;
		color: #666;
		padding: 0.3em;
		margin: 0.3em;
	}
	.redir input[type=text] {
		font-family: monospace;
		display: inline-block;
		width: 40%;
	}
	</style>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.cookie.js"></script>
</head>
<body>
<div class="toolbar">
	<h1>欧洲日志: 非洲血泪史</h1>
	<div><a href="#loot">掉落</a>&nbsp;|&nbsp;<a href="#constr">建造</a>&nbsp;|&nbsp;<a href="#dev">开发</a></div>
</div>
<br /><br />
<div class="box" id="loot">
<h2>掉落</h2>
<table style="width:100%;">
	<tr>
		<th>#</th>
		<th>掉落</th>
		<th>时间</th>
		<th>地图</th>
		<th>节点</th>
		<th>战斗类型</th>
		<th>战斗结果</th>
		<th>出货</th>
	</tr>
<?php

$sql = "SELECT * FROM kc_build_logs WHERE type='CONSTR' AND (fuel>50 OR ammo>50 OR steel>50 OR baux>50) ORDER BY date DESC";

$cmd = urldecode(substr($_SERVER['REQUEST_URI'], 1));
$arg = explode("/", $cmd);

$limit = 50;
if (isset($_COOKIE["limit"])) {
	$limit = intval($_COOKIE["limit"]);
}

$where = "fuel>50 OR ammo>50 OR steel>50 OR baux>50";
if (isset($_COOKIE["where"])) {
	$where = $_COOKIE["where"];
}

$order = "ORDER BY date DESC";
if (isset($_COOKIE["order"])) {
	$order = $_COOKIE["order"];
}

$where_loot = "baux=5 AND steel>5";
if (isset($_COOKIE["where_loot"])) {
	$where_loot = $_COOKIE["where_loot"];
}

?>

<?php
$sql = "SELECT * FROM kc_build_logs WHERE type='LOOT' AND ($where_loot) $order LIMIT $limit";
$logs = KCSql::inst()->querySql($sql);
$charset = "SABCDEFGHIJKLMNOPQRSTUVWXYZ-";
$battletype = array(2=>"资源",4=>"道中",5=>"Boss",6=>"无战斗");
$i = 0;
foreach ($logs as $entry) {
	++$i;
	echo "<tr class='shiprarity_".$db["ships"][$entry["product"]]["api_backs"]."'>\n";
	echo "<td>$i</td>\n";
	echo "<td>{$users[$entry["user"]]}</td>\n";
	echo "<td>{$entry["date"]}</td>\n";
	echo "<td>{$entry["fuel"]}-{$entry["ammo"]}</td>\n";
	echo "<td>".$charset[$entry["steel"]]."</td>\n";
	echo "<td>".$battletype[$entry["baux"]]."</td>\n";
	echo "<td>".$charset[$entry["seaweed"]]."</td>\n";
	echo "<td>{$db["ships"][$entry["product"]]["api_name"]}</td>\n";
	echo "</tr>\n";
}
?>
</table>
</div>

<div class="box" id="constr">
<h2>建造</h2>
<table style="width:100%;">
	<tr>
		<th>#</th>
		<th>建造</th>
		<th>时间</th>
		<th>油</th>
		<th>弹</th>
		<th>钢</th>
		<th>铝</th>
		<th>资材</th>
		<th>出货</th>
	</tr>
<?php
$sql = "SELECT * FROM kc_build_logs WHERE type='CONSTR' AND ($where) $order LIMIT $limit";
$logs = KCSql::inst()->querySql($sql);
$i=0;
foreach ($logs as $entry) {
	++$i;
	echo "<tr class='shiprarity_".$db["ships"][$entry["product"]]["api_backs"]."'>\n";
	echo "<td>$i</td>\n";
	echo "<td>{$users[$entry["user"]]}</td>\n";
	echo "<td>{$entry["date"]}</td>\n";
	echo "<td>{$entry["fuel"]}</td>\n";
	echo "<td>{$entry["ammo"]}</td>\n";
	echo "<td>{$entry["steel"]}</td>\n";
	echo "<td>{$entry["baux"]}</td>\n";
	echo "<td>{$entry["seaweed"]}</td>\n";
	echo "<td>{$db["ships"][$entry["product"]]["api_name"]}</td>\n";
	echo "</tr>\n";
}
?>
</table>
</div>

<div class="box" id="dev">
<h2>开发</h2>
<table style="width:100%;">
	<tr>
		<th>#</th>
		<th>开发</th>
		<th>时间</th>
		<th>油</th>
		<th>弹</th>
		<th>钢</th>
		<th>铝</th>
		<th>出货</th>
	</tr>
<?php
$sql = "SELECT * FROM kc_build_logs WHERE type='DEV' AND ($where) $order LIMIT $limit";
$logs = KCSql::inst()->querySql($sql);
$i=0;
foreach ($logs as $entry) {
	++$i;
	echo "<tr class='itemrarity_".$db["equipments"][$entry["product"]]["api_rare"]."'>\n";
	echo "<td>$i</td>\n";
	echo "<td>{$users[$entry["user"]]}</td>\n";
	echo "<td>{$entry["date"]}</td>\n";
	echo "<td>{$entry["fuel"]}</td>\n";
	echo "<td>{$entry["ammo"]}</td>\n";
	echo "<td>{$entry["steel"]}</td>\n";
	echo "<td>{$entry["baux"]}</td>\n";
	echo "<td>{$db["equipments"][$entry["product"]]["api_name"]}</td>\n";
	echo "</tr>\n";
}
?>
</table>
</div>

<div class="redir box">
	<script type="text/javascript">
	function filter_redir() {
		var limit = document.getElementById('cond_limit').value;
		var where = document.getElementById('cond_where').value;
		var order = document.getElementById('cond_order').value;
		var where_loot = document.getElementById('cond_where_loot').value;
		$.cookie("limit", limit, { expires: 30 });
		$.cookie("where", where, { expires: 30 });
		$.cookie("order", order, { expires: 30 });
		$.cookie("where_loot", where_loot, { expires: 30 });
		window.location.assign("build-logs.php");
	}
	function reset_filters() {
		$.removeCookie("limit");
		$.removeCookie("where");
		$.removeCookie("order");
		$.removeCookie("where_loot");
		window.location.assign("build-logs.php");
	}
	</script>
	<h2>过滤器</h2>
	<table>
		<tr>
			<td><strong>筛选条件</strong></td>
			<td>用户(DMM ID)</td>
			<td>时间(timestamp)</td>
			<td>油</td>
			<td>弹</td>
			<td>钢</td>
			<td>铝</td>
			<td>资材</td>
			<td>出货(船只ID)</td>
		</tr>
		<tr>
			<td><strong>列名称</strong></td>
			<td><code>user</code></td>
			<td><code>date</code></td>
			<td><code>fuel</code></td>
			<td><code>ammo</code></td>
			<td><code>steel</code></td>
			<td><code>baux</code></td>
			<td><code>seaweed</code></td>
			<td><code>product</code></td>
		</tr>
	</table>
	<a href="/kcapi/dmm-names.json" target="_blank" title="此页面需要登录">查询用户DMM id</a>
	&nbsp;
	<a href="/ships.php" target="_blank">查询船只id</a>
	<hr />
	数量(<code>LIMIT</code>): <input type="number" id="cond_limit" value="<?php echo $limit;?>" /><br />
	筛选(<code>WHERE</code>): <input type="text" id="cond_where" value="<?php echo $where;?>" /><br />
	排序(<code>ORDER</code>): <input type="text" id="cond_order" value="<?php echo $order;?>" /><br />
	<br />
	<table>
		<tr>
			<th>掉落筛选条件</th>
			<th>代码</th>
		</tr>
		<tr>
			<td>仅S胜</td>
			<td>seaweed=0</td>
		</tr>
		<tr>
			<td>仅Boss</td>
			<td>baux=5</td>
		</tr>
		<tr>
			<td>仅2-5</td>
			<td>(fuel=2 AND ammo=5)</td>
		</tr>
	</table>
	用括号(), AND和OR连接条件<br />
	筛选(<code>WHERE</code>): <input type="text" id="cond_where_loot" value="<?php echo $where_loot;?>" /><br />
	<input type="button" onclick="filter_redir()" value="应用设置" />
	<input type="button" onclick="reset_filters()" value="还原默认" />
</div>
</body>
</html>
