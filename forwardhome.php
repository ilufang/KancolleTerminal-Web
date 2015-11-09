<?php
/**
 *	forwardhome
 *
 *	Forwarding user's homepage
 *
 *	2015 by ilufang
 */

require_once "KCForwardUser.class.php";
$f_user = new KCForwardUser($user);

if (isset($_REQUEST['v']) && isset($f_user->token) && strlen($f_user->token)>0) {
	switch ($_REQUEST['v']) {
		case 'flash':
			header("Location: /kcs/mainD2.swf?api_token={$f_user->token}&api_starttime={$f_user->starttime}");
			break;
		case 'table':
			header("Location: /flashtable.php?token={$f_user->token}&starttime={$f_user->starttime}");
			break;
		case 'pages':
			header("Location: /flashpages.php?token={$f_user->token}&starttime={$f_user->starttime}");
			break;
		case 'web':
			header("Location: /flashcontainer.php?token={$f_user->token}&starttime={$f_user->starttime}");
			break;
	}
	die();
}

?>

<style type="text/css">
	.startlinks a {
		display: inline-block;
		text-decoration: none;
		background: none;
		font-size: 1.5em;
		padding: 0.5em;
		margin: -0.5em;
		border-radius: 0.2em;
	}
	.startlinks a:hover {
		background: #E3F2FD;
	}
	a, a:visited {
		color: blue;
	}
	table {
		border: 1px solid #666;
		border-collapse: collapse;
	}
	tr {
		border:1px dashed #ccc;
	}
	td {
		text-align: center;
	}
</style>



<div class="box startlinks">
	<h2>开始游戏</h2>

	<?php
		if (isset($f_user->token) && strlen($f_user->token)>0) {
			echo "<a href='/kcs/mainD2.swf?api_token={$f_user->token}&api_starttime={$f_user->starttime}'>以Flash运行游戏(全屏)</a><br /><br />\n";
			echo "<a href='/flashcontainer.php?token={$f_user->token}&starttime={$f_user->starttime}'>以网页运行游戏(固定大小)</a><br /><br />\n";
			echo "<a href='/flashtable.php?token={$f_user->token}&starttime={$f_user->starttime}'>以表格运行游戏(2x2)</a><br /><br />\n";
			echo "<a href='/flashpages.php?token={$f_user->token}&starttime={$f_user->starttime}'>分页运行游戏</a><br /><br />\n";
			echo "<a href='/flashviewer.php?token={$f_user->token}&starttime={$f_user->starttime}'>使用prophet运行(这玩意叫什么名字好呢...)</a><br /><br />\n";
			echo "<a href='http://{$f_user->server}/kcs/mainD2.swf?api_token={$f_user->token}&api_starttime={$f_user->starttime}' title='无法使用服务器专有功能'>直连API链接</a><br /><br />\n";
			echo "<span>最近一次链接更新于:{$f_user->lastupdate}</span>";
		} else {
			echo "请在下方[配置信息]处填写DMM登录凭据获取游戏链接 (建议开启HTTPS).\n";
		}
	?>
</div>

<script type="text/javascript">
	<?php
	if (array_key_exists("viewer", $_REQUEST)) {
		echo "var viewer = true;";
	} else {
		echo "var viewer = false;";
	}
	?>
	if (viewer) {
		window.location.assign(<?php echo "'/kcs/mainD2.swf?api_token={$f_user->token}&api_starttime={$f_user->starttime}'";?>);
	}
</script>

<div class="box">
	<script type="text/javascript">
	function bmlink() {
		alert("请右键链接复制地址或者拖拽至书签栏!");
		return false;
	}
	</script>
	<h2>服务器工具</h2>
	<strong>将以下地址书签或者设为viewer默认页可一键进入游戏</strong>
	<ul style="font-family: monospace, 'Courier New';">
		<li><a href="/v" onclick='return bmlink()'>kc.nfls.ga/v 全屏模式(Flash)</a></li>
		<li><a href="/w" onclick='return bmlink()'>kc.nfls.ga/w 网页模式(DMM大小)</a></li>
		<li><a href="/t" onclick='return bmlink()'>kc.nfls.ga/t 2x2表格</a></li>
		<li><a href="/p" onclick='return bmlink()'>kc.nfls.ga/p 分页模式</a></li>
	</ul>
	<hr />
	<a href="build-logs.php" class="button" target="_blank">开发建造日志</a>&nbsp;
	<a href="ships.php" class="button" target="_blank">船只信息</a>&nbsp;
	<a href="furniture/" class="button" target="_blank">家具信息</a>&nbsp;
	<a href="furniture/bgm.php" class="button" target="_blank">母港BGM信息</a>&nbsp;
	<a href="ban" class="button" target="_blank">投票封禁</a>&nbsp;
	<a href="filemgr" class="button"  target="_blank">文件管理(mod上传)</a>&nbsp;
	<br /><br />
	<a href="viewer/certgen.php" class="button" target="_blank">生成Viewer授权证书</a>&nbsp;
	<a href="viewer/prophet.php" class="button" target="_blank">Prophet</a>&nbsp;
	<br /><br />
	<input type="button" onclick="clearEntry()" value="清除入口缓存" />
	<script type="text/javascript">
	function clearEntry() {
		if (confirm("此操作会清除start2缓存, 下次加载将会较慢.\n你确定要清除吗?")) {
			$.ajax({
				url: "/servercmd.php/clearentry",
				method: "GET",
				success: function(data) {
					alert(data);
				},
				error: function() {
					alert("与服务器通信失败");
				}
			});
		}
	}
	</script>
</div>

<div class="box">
<?php
$gamedb = json_decode(file_get_contents("kcapi/gamedb.json"),true);

?>
	<h2>开发建造状态</h2>
	<table style="width:100%;">
		<tr>
			<th>建造</th>
			<th>油</th>
			<th>弹</th>
			<th>钢</th>
			<th>铝</th>
			<th>资材</th>
			<th>出货</th>
		</tr>
	<?php
	$logs = KCSql::inst()->querySql("SELECT * FROM kc_build_logs WHERE user='{$f_user->dmmid}' AND type='CONSTR' ORDER BY date DESC LIMIT 5");
	foreach ($logs as $entry) {
		echo "<tr>\n";
		echo "<td>{$entry["date"]}</td>\n";
		echo "<td>{$entry["fuel"]}</td>\n";
		echo "<td>{$entry["ammo"]}</td>\n";
		echo "<td>{$entry["steel"]}</td>\n";
		echo "<td>{$entry["baux"]}</td>\n";
		echo "<td>{$entry["seaweed"]}</td>\n";
		echo "<td>{$gamedb["ships"][$entry["product"]]["api_name"]}</td>\n";
		echo "</tr>\n";
	}
	?>
	</table>
	<br />
	<table style="width:100%;">
		<tr>
			<th>开发</th>
			<th>油</th>
			<th>弹</th>
			<th>钢</th>
			<th>铝</th>
			<th>出货</th>
		</tr>
	<?php
	$logs = KCSql::inst()->querySql("SELECT * FROM kc_build_logs WHERE user='{$f_user->dmmid}' AND type='DEV' ORDER BY date DESC LIMIT 5");
	foreach ($logs as $entry) {
		echo "<tr>\n";
		echo "<td>{$entry["date"]}</td>\n";
		echo "<td>{$entry["fuel"]}</td>\n";
		echo "<td>{$entry["ammo"]}</td>\n";
		echo "<td>{$entry["steel"]}</td>\n";
		echo "<td>{$entry["baux"]}</td>\n";
		echo "<td>{$gamedb["equipments"][$entry["product"]]["api_name"]}</td>\n";
		echo "</tr>\n";
	}
	?>
	</table>
	<hr />
</div>



<div class="box">
	<h2>翻译器</h2>
	替换服务器发回数据中的字符串,改变游戏动画的文字显示.(注意有些字符游戏无法显示,如部分简体中文,你可以通过游戏中的舰队名文本框进行测试)
	<table id="kcaccess" style="width:100%">
		<tr>
			<th width="15%">Type</th>
			<th width="40%">Subject</th>
			<th width="40%">Operation</th>
			<th width="5%">Options</th>
		</tr>
		<?php
			$count = 0;
			foreach ($f_user->kcaccess as $entry) {
				echo "<tr>\n";
				echo "<td><input type='text' value='{$entry['type']}' id='type$count' style='width:100%' /></td>\n";
				echo "<td><input type='text' value='{$entry['arg1']}' id='arg1$count' style='width:100%' /></td>\n";
				echo "<td><input type='text' value='{$entry['arg2']}' id='arg2$count' style='width:100%' /></td>\n";
				echo "<td><input type='text' value='{$entry['option']}' id='option$count' style='width:100%' /></td>\n";
				echo "</tr>\n";
				$count++;
			}
		?>
	</table>
	<script type="text/javascript">
	var KCAccess = {
		count: <?php echo $count?>,
		table: document.getElementById("kcaccess"),
		addRow: function() {
			var row = document.createElement("tr");
			row.innerHTML = "<td><input type='text' id='type"+this.count+"' style='width:100%' /></td>\n";
			row.innerHTML += "<td><input type='text' id='arg1"+this.count+"' style='width:100%' /></td>\n";
			row.innerHTML += "<td><input type='text' id='arg2"+this.count+"' style='width:100%' /></td>\n";
			row.innerHTML += "<td><input type='text' id='option"+this.count+"' style='width:100%' /></td>\n";
			this.table.appendChild(row);
			this.count++;
		},
		submitKCAccess: function() {
			var data = [];
			for (var i=0; i<this.count; i++) {
				var entry = {
					type: String(document.getElementById("type"+i).value),
					arg1: String(document.getElementById("arg1"+i).value),
					arg2: String(document.getElementById("arg2"+i).value),
					option: String(document.getElementById("option"+i).value)
				};
				if (entry.type.length>0 || entry.arg1.length>0 || entry.arg2.length>0 || entry.option.length>0) {
					data.push(entry);
				}
			}
			$.ajax({
				url: "editkcaccess.php",
				method: "POST",
				data: {
					token: user.token,
					kcaccess: JSON.stringify(data)
				},
				dataType: "json",
				failure: function() {
					alert("与服务器通信失败");
				},
				success: function(data) {
					if (data.success) {
						alert("替换规则已更新.\n空白行已删除");
						window.location.assign(window.location.href);
					} else {
						alert("请求错误:"+data.reason);
					}
				}
			});
		},
	};
	</script>
	<input type="button" onclick="KCAccess.addRow()" value="添加一行" />
	<hr/>
	<input type="button" onclick="KCAccess.submitKCAccess()" value="更新" />
</div>

<div class="box">
	<h2>配置信息</h2>
	<script type="text/javascript">
	function updateInfo() {
		document.getElementById('loginsubmit').disabled = true;
		var dmmid = document.getElementById('dmmid').value;
		var swfurl = document.getElementById('swfurl').value;
		var dmmuser = document.getElementById('dmmuser').value;
		var dmmpass = document.getElementById('dmmpass').value;
		$.ajax({
			url: "forwardupdate.php",
			method: "POST",
			data: {
				token: user.token,
//				dmmid: dmmid,
//				swfurl: swfurl,
				dmmuser: dmmuser,
				dmmpass: dmmpass
			},
			dataType: "json",
			error: function() {
				document.getElementById('loginsubmit').disabled = false;
				alert("与服务器通信失败");
			},
			success: function(data) {
				if (data.success) {
					alert("游戏链接已更新");
					window.location.assign(window.location.href);
				} else {
					document.getElementById('loginsubmit').disabled = false;
					alert("请求被拒绝:"+data.reason);
				}
			}
		});

	}
	</script>
	<form onsubmit="updateInfo();return false;">
		<span id="update_status">服务器不会保存DMM登录信息, 建议通过HTTPS登录</span><br />
		<input type="text" id="dmmuser" placeholder="DMM用户名(Email)" /><br />
		<input type="password" id="dmmpass" placeholder="DMM登录密码" /><br />
		<div style="display:none">
			<input type="text" id="dmmid" placeholder="DMM ID" value="<?php
			if (isset($f_user->dmmid)) {
				echo $f_user->dmmid;
			}?>"/><br />
			<input type="text" id="swfurl" placeholder="Flash URL" value="<?php
			if (isset($f_user->token)) {
				echo "http://{$f_user->server}/kcs/mainD2.swf?api_token={$f_user->token}&api_starttime={$f_user->starttime}";
			}
			?>"/><br />
		</div>
		<input type="submit" value="更新链接" />
	</form>
</div>


<div class="box">
	<h2>发包器</h2>
	<p>
		用于战斗过程猫紧急补发. 请注意战斗开始和获取战斗结果间应至少暂停30秒以上(越长越好), 否则你有可能被识别为BOT.
	</p>
		<div>
		<script type="text/javascript">
			var requestRegions = {
				activated: 0,
				openView: function(id) {
					if (this.activated == id) {
						document.getElementById('req_'+id).style.display = "none";
						this.activated = 0;
					} else {
						document.getElementById('req_'+this.activated).style.display = "none";
						document.getElementById('req_'+id).style.display = "block";
						this.activated = id;
					}
				},
				request: function(uri, arg) {
					$.ajax({
						url: uri,
						method: "POST",
						headers: {
							"Referer": document.getElementById("swfurl").value+"/[[DYNAMIC]]/1",
							"X-Requested-With": "ShockwaveFlash/17.0.0.134"
						},
						data: arg,
						failure: function() {
							alert("请求失败");
						},
						success: function(data) {
							document.getElementById("req_result").innerHTML = data;
						}
					});
				},
				requestBattle: function() {
					this.request("/kcsapi/api_req_sortie/battle", {
						api_token:user.token,
						api_verno: 1,
						api_recovery_type:0,
						api_formation_id: parseInt(document.getElementById('req_battle_formation').value)
					});
				},
				requestNight: function() {
					this.request("/kcsapi/api_req_battle_midnight/battle", {
						api_token:user.token,
						api_verno: 1,
						api_recovery_type:0
					});
				},
				requestResult: function() {
					this.request("/kcsapi/api_req_sortie/battleresult", {
						api_token:user.token,
						api_verno: 1
					});
				},
				requestNext: function() {
					this.request("/kcsapi/api_req_map/next", {
						api_token:user.token,
						api_verno: 1,
						api_recovery_type:0
					});
				},
				requestCombined: function() {
					this.request("/kcsapi/api_req_hensei/combined", {
						api_token:user.token,
						api_verno: 1,
						api_combined_type: parseInt(document.getElementById('req_combined_action').value)
					});
				},
				requestTouchStart2: function() {
					this.request("/kcsapi/api_start2", {
						api_token:user.token,
						api_verno: 1
					});
				},
				requestCustom: function() {
					this.request(document.getElementById('req_custom_uri').value, JSON.parse(document.getElementById('req_custom_post').value));
				}
			}
		</script>
		<input type="button" value="开始战斗" onclick="requestRegions.openView(1)" />
		<input type="button" value="开始战斗(夜战)" onclick="requestRegions.openView(2)" />
		<input type="button" value="获取战斗结果" onclick="requestRegions.openView(3)" />
		<input type="button" value="进击" onclick="requestRegions.openView(4)" />
		<input type="button" value="联合编队" onclick="requestRegions.openView(5)" />
		<input type="button" value="Touch Start2" onclick="requestRegions.openView(7)" />
		<input type="button" value="自定义" onclick="requestRegions.openView(6)" />
	</div>
	<div id="req_0"><!-- Prevent javascript interruption by unknown id exp_0 --></div>
	<div id="req_1" style="display:none">
		<h3>开始战斗</h3>
		<input type="text" id="req_battle_formation" placeholder="阵型ID"/><br />
		<input type="button" onclick="requestRegions.requestBattle()" value="发送" />
	</div>
	<div id="req_2" style="display:none">
		<h3>开始战斗(夜战)</h3>
		<input type="button" onclick="requestRegions.requestNight()" value="发送" />
	</div>
	<div id="req_3" style="display:none">
		<h3>获取战斗结果</h3>
		<input type="button" onclick="requestRegions.requestResult()" value="发送" />
	</div>
	<div id="req_4" style="display:none">
		<h3>进击</h3>
		<input type="button" onclick="requestRegions.requestNext()" value="发送" />
	</div>
	<div id="req_5" style="display:none">
		<h3>联合编队</h3>
		<select id="req_combined_action" selected="0">
			<option value="0">解除联合编队</option>
			<option value="1">编成机动部队</option>
			<option value="2">编成水上部队</option>
		</select>
		<input type="button" onclick="requestRegions.requestCombined()" value="发送" />
	</div>
	<div id="req_7" style="display:none">
		<h3>Touch start2</h3>
		<input type="button" onclick="requestRegions.requestTouchStart2()" value="发送" />
	</div>
	<div id="req_6" style="display:none">
		<h3>自定义</h3>
		<strong>警告:慎用!</strong><br />
		URI:<input type="text" id="req_custom_uri" value="/kcsapi/" style="width:75%"/><br />
		Args: (json)<br />
		<textarea id="req_custom_post" style="font-family:monospace;width:100%">
{
"api_token": "<?php echo $f_user->token;?>",
"api_verno": 1
}</textarea>
		<br />
		<input type="button" onclick="requestRegions.requestCustom()" value="发送" />
	</div>
	<div id="req_result"></div>
</div>
