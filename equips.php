<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title>Equipments</title>
	<style type="text/css">
	body {
		font-family: monospace;
	}
	table {
		border-collapse: collapse;
	}
	td {
		border: 1px solid #ccc;
	}
	a:link, a:visited {
		color: blue;
	}
	</style>
</head>
<body>
<table>
	<tr>
		<th>#</th>
		<th>图鉴</th>
		<th>名称</th>
		<th>卡片</th>
		<th>详细信息</th>
		<th style='width:3em'>装甲</th>
		<th style='width:3em'>火力</th>
		<th style='width:3em'>雷装</th>
		<th style='width:3em'>爆装</th>
		<th style='width:3em'>对空</th>
		<th style='width:3em'>对潜</th>
		<th style='width:3em'>命中</th>
		<th style='width:3em'>回避</th>
		<th style='width:3em'>索敌</th>
		<th style='width:3em'>射程</th>
		<th style='width:3em'>稀有度</th>
	</tr>
	<?php
	function cardPath($id) {
		$path = "/kcs/resources/image/slotitem/card/";
		if ($id<10) {
			$path.="00";
		} else if ($id<100) {
			$path.="0";
		}
		$path.=$id;
		$path.=".png";
		return $path;
	}
	$db = json_decode(file_get_contents("kcapi/gamedb.json"), true);
	foreach ($db["equipments"] as $equip) {
		if (!isset($equip["api_id"])) {
			continue;
		}
		echo "<tr>\n";
		echo "<td id='ship_$equip[api_id]'>$equip[api_id]</td>\n";
		echo "<td>$equip[api_sortno]</td>\n";
		echo "<td>$equip[api_name]</td>\n";
		echo "<td><img src='".cardPath($equip['api_id'])."' style='width:8em' alt='$equip[api_name]'/></td>\n";
		echo "<td>$equip[api_info]</td>\n";
		echo "<td>$equip[api_souk]</td>\n";
		echo "<td>$equip[api_houg]</td>\n";
		echo "<td>$equip[api_raig]</td>\n";
		echo "<td>$equip[api_baku]</td>\n";
		echo "<td>$equip[api_tyku]</td>\n";
		echo "<td>$equip[api_tais]</td>\n";
		echo "<td>$equip[api_houm]</td>\n";
		echo "<td>$equip[api_houk]</td>\n";
		echo "<td>$equip[api_saku]</td>\n";
		echo "<td>$equip[api_leng]</td>\n";
		echo "<td>$equip[api_rare]</td>\n";

		echo "</tr>\n";
	}
	?>
</table>
</body>
</html>
