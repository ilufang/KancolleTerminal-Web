<?php
/**
 *	init.js
 *	start2 loader&cache mgr
 */
date_default_timezone_set("Asia/Shanghai");
$filehash = substr(sha1_file("../kcapi/start2.json"), -8);
if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
	$reqtags = explode("-", substr($_SERVER['HTTP_IF_NONE_MATCH'], 1, -1));
	$reqhash = $reqtags[1];
	$reqtime = $reqtags[0];
	if (time() - intval($reqtime) < 86400 && $filehash === $reqhash) {
		// Cache within 1 day AND start2 not changed
		header("HTTP/1.1 304 Not Modified");
		die();
	}
}
header("Content-Type: application/javascript");
header("Etag: \"".time()."-".$filehash."\"");
require_once '../KCUser.class.php';
$user = new KCUser();
if (isset($_REQUEST['user']) && $_REQUEST['user'] && $user->initWithID($_REQUEST['user'])) {
?>
notify("初始化数据...");
var <?php
echo file_get_contents("http://$config[serveraddr]/kcsapi/api_start2?api_token=".$user->token);
?>;
poi.resolve({detail:{path:"/kcsapi/api_start2", body:svdata.api_data, method:"POST", postBody: {}}});
initData();
<?php
}
