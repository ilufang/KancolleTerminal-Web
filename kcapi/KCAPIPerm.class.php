<?php

class KCAPIPerm
{
	public static function beforeRequest($req) {
		if (!file_exists("permissions.json")) {
			file_put_contents("permissions.json", "{}");
		}
		$permdb = json_decode(file_get_contents("permissions.json"),TRUE);
		$uri = explode("?", $req->uri)[0];
		if (!isset($permdb[$uri])) {
			$permdb[$uri]=TRUE;
			ksort($permdb);
			file_put_contents("permissions.json", json_encode($permdb));
		}
		if ($permdb[$uri]===FALSE) {
			$req->req_type="REJECTED";
			$req->errno=403;
		}
	}
}
