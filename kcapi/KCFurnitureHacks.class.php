<?php
/**
 *	KCFurnitureHacks
 *
 *	Request Regex replace creator and response handler for client-side furniture hacking
 *
 *	2015 by ilufang
 */

// $replaceRule = "\"api_furniture\":\\[(\\d+),(\\d+),(\\d+),(\\d+),(\\d+),(\\d+)\\]";

class KCFurnitureHacks {

private $user, $initialized;
private $ruleFound, $ruleidx;

function __construct($user) {
	$this->initialized = false;
	$this->user = $user;
	$this->ruleFound = false;
	$this->ruleidx = 0;
	foreach ($user->kcaccess as $rule) {
		if ($rule['type']==="furniture") {
			$this->initialized = true;
		} else {
		}
		if ($rule['type']==="PregReplace" && $rule['arg1']==="\"api_furniture\":\\[(\\d+),(\\d+),(\\d+),(\\d+),(\\d+),(\\d+)\\]") {
			$this->ruleFound = true;
		}
		if (!$this->ruleFound) {
			$this->ruleidx++;
		}
	}
}

function beforeRequest($req) {
	if (!$this->initialized) {
		return;
	}
	if ($req->uri==="/kcsapi/api_req_furniture/change") {
		// Record new furniture data, commit to database and reset the connection
		$req->errno = 1;
		$req->req_type="WRITTEN";

		$new_furn = array(intval($_REQUEST['api_floor']), intval($_REQUEST['api_wallpaper']), intval($_REQUEST['api_window']), intval($_REQUEST['api_wallhanging']), intval($_REQUEST['api_shelf']), intval($_REQUEST['api_desk']));
		$newrule = "\"api_furniture\":".json_encode($new_furn);
		$this->user->kcaccess[$this->ruleidx] = array("type"=>"PregReplace", "arg1"=>"\"api_furniture\":\\[(\\d+),(\\d+),(\\d+),(\\d+),(\\d+),(\\d+)\\]", "arg2"=>$newrule, "option"=>"");
		KCSql::inst()->update(array("kcaccess"=>utf8_encode(json_encode($this->user->kcaccess))),"forward_users")->where("memberid={$this->user->id}")->query();
	}
}

function afterRequest($req) {
	if (!$this->initialized) {
		return;
	}
	if ($req->uri==="/kcsapi/api_get_member/furniture") {
		// Set as all furnitures purchased
		$obj = $req->response;
		$memberid = $obj[0]['api_member_id'];
		$db = json_decode(file_get_contents("gamedb.json"), true);
		$badfurns = json_decode(file_get_contents("badfurns.json"), true);
		$full_furn = array();
		foreach ($db['furniture'] as $key => $value) {
			if (in_array($key, $badfurns))
				continue;

			$full_furn[] = array("api_member_id"=>$memberid,
			                     "api_id"=>$key,
			                     "api_furniture_type"=>$value['api_type'],
			                     "api_furniture_no"=>$value['api_no'],
			                     "api_furniture_id"=>$key
			                     );
		}
		$req->response = $full_furn;
	} else if ($req->uri==="/kcsapi/api_port/port") {
		// Initialize user furniture if not set yet
		if (!$this->ruleFound) {
			$newrule = "\"api_furniture\":".json_encode($req->response['api_basic']['api_furniture']);
			$this->user->kcaccess[] = array("type"=>"PregReplace", "arg1"=>"\"api_furniture\":\\[(\\d+),(\\d+),(\\d+),(\\d+),(\\d+),(\\d+)\\]", "arg2"=>$newrule, "option"=>"");
			KCSql::inst()->update(array("kcaccess"=>utf8_encode(json_encode($this->user->kcaccess))),"forward_users")->where("memberid={$this->user->id}")->query();
		}
	}
}

}
?>
