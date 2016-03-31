<?php
header("Content-Type: text/plain; charset=utf-8");
if (!isset($argv) || !$argv) {
	$cmd = substr($_SERVER['REQUEST_URI'], 1); // Shift out preceding slash
	$argv = explode("/", $cmd);
}
if (!isset($argv[1])) {
	die("No Command.\n");
}
switch ($argv[1]) {
	case 'update':
		require_once 'KCSql.class.php';
		if(!KCSql::inst()->update(array("token"=>"","starttime"=>0),"forward_users")->query()) {
			echo "SQL Error: ".KCSql::inst()->error();
			break;
		} else {
			echo "用户token已清除.\n";
		}
		// Fall thru
	case 'clearentry':
		unlink("kcapi/start2.json");
		unlink("kcres/files/kcs/mainD2.swf");
		unlink("kcres/files/kcs/Core.swf");
		echo "入口缓存已清除.";
		break;
	default:
		echo "Unknown command $argv[1]";
}
echo "\n";
