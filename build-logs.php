<?php
require_once 'KCSql.class.php';
$db = json_decode(file_get_contents("kcapi/gamedb.json"),true);
$users = json_decode(file_get_contents("kcapi/dmm-names.json"),true);
?>
<!doctype html>
<html>
<head>
	<title>欧洲日志</title>
	<style type="text/css">
	table {
		border: 1px solid #666;
		border-collapse: collapse;
		font-family: monospace;
	}
	tr {
		border:1px dashed #ccc;
	}
	td {
		text-align: center;
	}
</style>

</head>
<body>
<h2>开发.建造日志</h2>
<table style="width:100%;">
	<tr>
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
$logs = KCSql::inst()->querySql("SELECT * FROM kc_build_logs WHERE type='CONSTR' ORDER BY date DESC LIMIT 50");
foreach ($logs as $entry) {
	echo "<tr>\n";
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
<br />
<table style="width:100%;">
	<tr>
		<th>开发</th>
		<th>时间</th>
		<th>油</th>
		<th>弹</th>
		<th>钢</th>
		<th>铝</th>
		<th>出货</th>
	</tr>
<?php
$logs = KCSql::inst()->querySql("SELECT * FROM kc_build_logs WHERE type='DEV' AND product!=-1 ORDER BY date DESC LIMIT 50");
foreach ($logs as $entry) {
	echo "<tr>\n";
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

</body>
</html>
