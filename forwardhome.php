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
?>
<div class="box" style="width:80%">
	<h2>开始游戏</h2>

	<?php
		if (isset($f_user->token)) {
			echo "<a href='/kcs/mainD2.swf?api_token={$f_user->token}&api_starttime={$f_user->starttime}' style='font-size:1.5em'>打开游戏主Flash</a><br /><br />\n";
			echo "<span>最近一次链接更新于:{$f_user->lastupdate}</span>";
		} else {
			echo "请按下面的指令操作, 获取swf链接.\n";
		}
	?>
</div>

<div class="box" style="width:80%">
	<h2>配置信息</h2>
	<script type="text/javascript">
	function updateInfo() {
		var dmmid = document.getElementById('dmmid').value;
		var swfurl = document.getElementById('swfurl').value;

		$.ajax({
			url: "forwardupdate.php",
			method: "POST",
			data: {
				token: user.token,
				dmmid: dmmid,
				swfurl: swfurl
			},
			dataType: "json",
			failure: function() {
				alert("与服务器通信失败");
			},
			success: function(data) {
				if (data.success) {
					alert("数据已更新,请刷新本页面或者重新登录舰队以更新链接.");
				} else {
					alert("请求被拒绝:"+data.reason);
				}
			}
		});

	}
	</script>
	<form onsubmit="updateInfo();return false;">
		<span id="update_status">你只需要填写一项. 填过DmmID的就无需更新此表格.</span><br />
		<input type="text" id="dmmid" placeholder="DMM ID" value="<?php
		if (isset($f_user->dmmid)) {
			echo $f_user->dmmid;
		}?>"/><br />
		<input type="text" id="swfurl" placeholder="Flash URL" value="<?php
		if (isset($f_user->token)) {
			echo "http://{$f_user->server}/kcs/mainD2.swf?api_token={$f_user->token}&api_starttime={$f_user->starttime}";
		}
		?>"/><br />
		<input type="submit" value="Update" />
	</form>
	<div>
		<script type="text/javascript">
			var explanRegions = {
				activated: 0,
				openView: function(id) {
					if (this.activated == id) {
						document.getElementById('exp_'+id).style.display = "none";
						this.activated = 0;
					} else {
						document.getElementById('exp_'+this.activated).style.display = "none";
						document.getElementById('exp_'+id).style.display = "block";
						this.activated = id;
					}
				}
			}
		</script>
		<input type="button" value="如何配置" onclick="explanRegions.openView(1)" />
		<input type="button" value="获取swf链接" onclick="explanRegions.openView(2)" />
		<input type="button" value="获取DMM ID" onclick="explanRegions.openView(3)" />
	</div>
	<div id="exp_0"><!-- Prevent javascript interruption by unknown id exp_0 --></div>
	<div id="exp_1" style="display:none">
		<h3>如何配置</h3>
		我们需要获得关于你游戏的相关信息才能正确将你的请求发送至指定服务器. 你可以:
		<ol>
			<li>手动获取swf链接,并将其填入下方[Flash链接]一栏中</li>
			<li>填入你的DMM ID, 由服务器自动抓取</li>
		</ol>
		具体的获取方法请见下面<br />
		<br />
	</div>
	<div id="exp_2" style="display:none">
		<h3>获取swf链接</h3>
		<hr />
		你可以通过提供指向游戏入口的Flash链接提供该数据. 但请注意每次你通过dmm重登录或通过第三方登录工具登录后, 你的swf链接会更新, 原有的swf链接会失效, 你必须每次重新获取链接并填入下方.<br /><br />
		要获取swf链接:
		<ol>
			<li>在游戏页面右键,选择[审查元素]</li>
			<li>在审查元素界面进行搜索, 你可以通过按Ctrl-F或Cmd-F调出搜索框</li>
			<li>搜索mainD2.swf</li>
			<li>将匹配处的完整链接复制出来</li>
		</ol>
		链接的格式应为<code>http://(域名/IP)/kcs/mainD2.swf?api_token=(40位16进制数)&api_token=(一较大整数)</code>,当你将这个链接直接在浏览器中打开时, 你应该能直接看到游戏全屏运行<br />
		<br />
	</div>
	<div id="exp_3" style="display:none">
		<h3>获取DMM ID</h3>
		我们推荐使用DmmID动态地抓取链接, 只要你通过osapi链接访问, 我们就一直会存储着您的最新的有效的游戏链接.(注意使用舰队司令部加速将会不通过osapi刷新链接,此时您必须重新登录)<br />
		要获取DmmID, 请打开DMM中的舰队页面, 在地址栏中输入<code>javascript:prompt("Your DMM ID is",gadgetInfo.OWNER_ID);</code>
		<br />
		另外, 要使我们能正确处理到你经过osapi的请求, 您必须将<code>osapi.dmm.com</code>的域名解析指向我们的服务器,要这样,请打开您的<code>hosts</code>文件, 并在末尾添加如下一行:<br />
		<code>
		223.223.218.205 osapi.dmm.com
		</code><br />
		Hosts文件一般存储在这里 (您可能需要修改文件安全权限或成为root以修改文件,若仍有问题请搜索"修改hosts"):
		<ul>
			<li>Windows: <code>C:\Windows\System32\drivers\etc\hosts</code></li>
			<li>MacOS/Linux: <code>/etc/hosts</code></li>
			<li>Android: <code>/system/etc/hosts</code></li>
			<li>iOS: 反正你不能放Flash, 若你能跑Flash那你肯定也能改hosts :XD</li>
		</ul>
		如果你曾经修改过<code>osapi.dmm.com</code>的hosts记录, 请覆写.<Br />
		<Br />
		请使用以下方式登录舰队,这样我们就可以获取您的链接了
		<ul>
			<li>通过DMM官方登录:<a href="http://www.dmm.com/netgame/social/-/gadgets/=/app_id=854854/">艦これ</a></li>
			<li>通过舰娘登陆器(推荐,无地区限制): <a href="https://kancolle.tv/connector1/">入口一</a> | <a href="https://kancolle.tv/connector2/">入口二</a></li>
		</ul>
		获取后, 你就可以直接通过访问本站点, 点击页面上方链接直接开始游戏了
	</div>

</div>

<div class="box" style="width:80%">
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
						alert("数据已更新.\n空白项已被删除.");
					} else {
						alert("请求错误:"+data.reason);
					}
				}
			});
		}
	};
	</script>
	<input type="button" onclick="KCAccess.addRow()" value="Add Row" />
	<hr/>
	<input type="button" onclick="KCAccess.submitKCAccess()" value="Update" />
</div>

<div class="box" style="width:80%">
	<h2>发包器</h2>
	<p>
		用于战斗过程猫紧急补发. 请注意战斗开始和获取战斗结果间应至少暂停30秒以上(越长越好), 否则你有可能被识别为BOT.
	</p>
	<h3>参考</h3>
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
				requestCustom: function() {
					this.request(document.getElementById('req_custom_uri').value, JSON.parse(document.getElementById('req_custom_post').value));
				}
			}
		</script>
		<input type="button" value="开始战斗" onclick="requestRegions.openView(1)" />
		<input type="button" value="开始战斗(夜战)" onclick="requestRegions.openView(2)" />
		<input type="button" value="获取战斗结果" onclick="requestRegions.openView(3)" />
		<input type="button" value="进击" onclick="requestRegions.openView(4)" />
		<input type="button" value="自定义" onclick="requestRegions.openView(5)" />
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
		<h3>自定义</h3>
		<strong>警告:慎用!</strong><br />
		URI:<input type="text" id="req_custom_uri" value="/kcsapi/" /><br />
		Args: (json)<br />
		<textarea id="req_custom_post" style="font-family:monospace"></textarea>
		<input type="button" onclick="requestRegions.requestCustom()" value="发送" />
	</div>
	<p id="req_result" style="font-family:monospace"></p>

</div>

<style type="text/css">
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

<div class="box" style="width:80%">
<?php
$gamedb = json_decode(file_get_contents("kcapi/gamedb.json"),true);

?>
	<h2>日志</h2>
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
	<a href="/build-logs.php">More</a>
</div>
