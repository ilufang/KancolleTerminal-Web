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
	</tr>
	<?php
	$db = json_decode(file_get_contents("kcapi/gamedb.json"), true);
	foreach ($db["equipments"] as $equip) {
		if (!isset($equip["api_id"])) {
			continue;
		}
		echo "<tr>\n";
		echo "<td id='ship_$equip[api_id]'>$equip[api_id]</td>\n";
		echo "<td>$equip[api_sortno]</td>\n";
		echo "<td>\"$equip[api_name]\":\"\",</td>\n";
		echo "</tr>\n";
	}
	?>
</table>
</body>
</html>
