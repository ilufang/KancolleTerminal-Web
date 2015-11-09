<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>BGM</title>
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
<table>
	<tr>
		<th>ID</th>
		<th>曲名</th>
	</tr>
	<?php
	$start2 = file_get_contents("../kcapi/start2.json");
	$db = json_decode($start2, true);
	foreach ($db["api_mst_bgm"] as $bgm) {
		echo "<tr>";
		echo "<td>$bgm[api_id]</td>";
		echo "<td>$bgm[api_name]</td>";
		echo "</tr>\n";
	}
	?>
</table>
</body>
</html>
