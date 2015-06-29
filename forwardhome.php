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
<h2>开始游戏</h2>
<hr />
<?php
	if (isset($f_user->token)) {
		echo "<a href='/kcs/mainD2.swf?api_token={$f_user->token}&api_starttime={$f_user->starttime}' style='font-size:1.5em'>打开游戏主Flash</a><br />\n";
		echo "<span>最近一次链接更新于:{$f_user->lastupdate}</span><br />";
	} else {
		echo "请按下面的指令操作, 获取swf链接.<br />\n";
	}
?>
<br />
<h2>配置信息</h2>
<hr />
<script type="text/javascript">
function updateInfo() {
	var dmmid = document.getElementById('dmmid').value;
	var swfurl = document.getElementById('swfurl').value;

	$.ajax({
		url: "forwardupdate.php",
		method: "POST",
		data: {
			username: user.username,
			passhash: user.passhash,
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
<br />
<br />
<h2>如何配置</h2>
<hr />
我们需要获得关于你游戏的相关信息才能正确将你的请求发送至指定服务器. 你可以:
<ol>
	<li>手动获取swf链接,并将其填入下方[Flash链接]一栏中</li>
	<li>填入你的DMM ID, 由服务器自动抓取</li>
</ol>
具体的获取方法请见下面<br />
<br />
<h2>获取swf链接</h2>
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
<h3>获取DMM ID</h3>
<hr />
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
获取后, 你就可以直接通过访问本站点, 点击页面上方链接直接开始游戏了 (KCV兼容)


