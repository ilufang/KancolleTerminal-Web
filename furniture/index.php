<?php
function addZeros($num) {
	$num++;
	if ($num<10) {
		return "00$num";
	} else if ($num<100) {
		return "0$num";
	} else {
		return "$num";
	}
}
?>
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
			<th>Img</th>
		</tr>
		<?php
		$typeurl = array("floor","wall","window","object","chest","desk");
		$db = json_decode(file_get_contents("../kcapi/gamedb.json"),true);
		foreach ($db["furniture"] as $furniture) {
			echo "<tr>\n";
			echo "<td>$furniture[api_id]</td>\n";
			echo "<td>$furniture[api_type]</td>\n";
			echo "<td>$furniture[api_no]</td>\n";
			echo "<td>$furniture[api_title]</td>\n";
			echo "<td>$furniture[api_description]</td>\n";
			echo "<td>$furniture[api_price]</td>\n";
			echo "<td>$furniture[api_rarity]</td>\n";
			echo "<td><img src='/kcs/resources/image/furniture/".$typeurl[$furniture["api_type"]]."/".addZeros($furniture["api_no"]).".png'/></td>\n";
		}
		?>
	</table>
	</body>
</html>
