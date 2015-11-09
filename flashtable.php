<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<title>舰队Collection - NFLS.GA</title>
	<style type="text/css">
	body {
		background: black;
		padding: 0px;
		margin: 0px;
	}
	table{
		border: none;
		border-collapse: collapse;
		width: 100%;
		height: 100%;
		padding: 0px;
		margin: 0px;
	}
	tr {
		border: none;
		height: 50%;
		padding: 0px;
		margin: 0px;
	}
	td {
		border: none;
		width: 50%;
		padding: 0px;
		margin: 0px;
	}
	iframe {
		border: none;
		width: 100%;
		height: 100%;
		padding: 0px;
		margin: 0px;
	}
	</style>
	<script type="text/javascript">
	var resizeTable = function() {
		document.getElementById('table').style.height = window.innerHeight+"px";
	}
	window.onresize = resizeTable;
	window.onload = resizeTable;

	</script>
</head>
<body>
<?php
$flashurl="/kcs/mainD2.swf?api_token=$_REQUEST[token]&api_starttime=$_REQUEST[starttime]";
?>
<table id="table">
	<tr>
		<td>
			<iframe src="<?php echo $flashurl;?>"></iframe>
		</td>
		<td>
			<iframe src="<?php echo $flashurl;?>"></iframe>
		</td>
	</tr>
	<tr>
		<td>
			<iframe src="<?php echo $flashurl;?>"></iframe>
		</td>
		<td>
			<iframe src="<?php echo $flashurl;?>"></iframe>
		</td>
	</tr>
</table>
</body>
</html>
