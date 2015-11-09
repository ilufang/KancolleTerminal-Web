<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>Furniture</title>
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
	<h1>家具</h1>
	<table>
		<tr>
			<th>ID</th>
			<th>类型</th>
			<th>序号</th>
			<th>名称</th>
			<th>描述</th>
			<th>价格</th>
			<th>稀有度</th>
		</tr>
		<?php
		$db = json_decode(file_get_contents("kcapi/gamedb.json"),true);
		foreach ($db["furniture"] as $furniture) {
			echo "<tr>\n";
			echo "<td>$furniture[api_id]</td>\n";
			echo "<td>$furniture[api_type]</td>\n";
			echo "<td>$furniture[api_no]</td>\n";
			echo "<td>$furniture[api_title]</td>\n";
			echo "<td>$furniture[api_description]</td>\n";
			echo "<td>$furniture[api_price]</td>\n";
			echo "<td>$furniture[api_rarity]</td>\n";
		}
		?>
	</table>
	<hr />
	<h1>备注:无法获取的资源(ID为上表中序号)</h1>
	<ul>
		<li>壁纸:6, 25, 29</li>
		<li>地板:8, 10, 11, 13, 15, 16, 18, 19, 23, 26-30, 32, 36</li>
	</ul>
	</body>
</html>
