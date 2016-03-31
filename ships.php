<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title>Ships</title>
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
		<th>种类</th>
		<th>稀有度</th>
		<th>火力</th>
		<th>雷装</th>
		<th>对空</th>
		<th>装甲</th>
		<th>运</th>
		<th>搭载</th>
		<th>建造</th>
		<th>改造</th>
		<th>文件资源</th>
		<!-- Remove these 2 colume if you cannot use swf2img.php -->
		<th>图片资源</th>
		<th>操作</th>
	</tr>
	<?php
	$db = json_decode(file_get_contents("kcapi/gamedb.json"), true);
	foreach ($db["ships"] as $ship) {
		if (!isset($ship["api_id"])) {
			//continue;
		}
		echo "<tr>\n";
		echo "<td id='ship_$ship[api_id]'>$ship[api_id]</td>\n";
		echo "<td>$ship[api_sortno]</td>\n";
		echo "<td>$ship[api_name]</td>\n";
		echo "<td>$ship[api_stype]</td>\n";
		echo "<td>$ship[api_backs]</td>\n";
		echo "<td>".$ship["api_houg"][0]."/".$ship["api_houg"][1]."</td>\n";
		echo "<td>".$ship["api_raig"][0]."/".$ship["api_raig"][1]."</td>\n";
		echo "<td>".$ship["api_tyku"][0]."/".$ship["api_tyku"][1]."</td>\n";
		echo "<td>".$ship["api_souk"][0]."/".$ship["api_souk"][1]."</td>\n";
		echo "<td>".$ship["api_luck"][0]."/".$ship["api_luck"][1]."</td>\n";
		echo "<td>".$ship["api_maxeq"][0]."/".$ship["api_maxeq"][1]."/".$ship["api_maxeq"][2]."/".$ship["api_maxeq"][3]."</td>\n";
		echo "<td>".floor($ship["api_buildtime"]/60).":".($ship["api_buildtime"]%60)."</td>\n";
		if ($ship["api_aftershipid"]!=0) {
			echo "<td><a href='#ship_$ship[api_aftershipid]'>Lv.$ship[api_afterlv]($ship[api_afterfuel]/$ship[api_afterbull])</a></td>\n";
		} else {
			echo "<td></td>";
		}
		echo "<td><a href='/kcs/resources/swf/ships/$ship[api_filename].swf'>$ship[api_filename]</a></td>\n";
		echo "<td><a href='/kcres/shipimg.php?id=".(isset($ship["api_id"])?$ship["api_id"]:$ship["api_filename"])."' target='_blank'><img src='/kcres/images/ship/".(isset($ship["api_id"])?$ship["api_id"]:$ship["api_filename"])."/1.png'/></a></td>\n"; // Remove this if you could not use swf2img.php
		echo "<td><a href='/kcres/updateimg.php?".(isset($ship['api_id'])?("id=".$ship['api_id']):("filename=".$ship['api_filename']))."' target='_blank'>更新图片</a></td>\n"; // Remove this if you could not use swf2img.php
		echo "</tr>\n";
	}
	?>
</table>
</body>
</html>
