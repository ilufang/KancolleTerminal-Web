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
	if ($req->uri==="/kcsapi/api_get_member/require_info") {
		// Set as all furnitures purchased
		$obj = $req->response['api_furniture'];
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
		$full_furn[] = array("api_member_id"=>$memberid,
		                     "api_id"=>990,
		                     "api_furniture_type"=>0,
		                     "api_furniture_no"=>99,
		                     "api_furniture_id"=>990
		                     );
		$full_furn[] = array("api_member_id"=>$memberid,
		                     "api_id"=>999,
		                     "api_furniture_type"=>1,
		                     "api_furniture_no"=>99,
		                     "api_furniture_id"=>999
		                     );
		$full_furn[] = array("api_member_id"=>$memberid,
		                     "api_id"=>992,
		                     "api_furniture_type"=>2,
		                     "api_furniture_no"=>99,
		                     "api_furniture_id"=>992
		                     );
		$full_furn[] = array("api_member_id"=>$memberid,
		                     "api_id"=>993,
		                     "api_furniture_type"=>3,
		                     "api_furniture_no"=>99,
		                     "api_furniture_id"=>993
		                     );
		$full_furn[] = array("api_member_id"=>$memberid,
		                     "api_id"=>994,
		                     "api_furniture_type"=>4,
		                     "api_furniture_no"=>99,
		                     "api_furniture_id"=>994
		                     );
		$full_furn[] = array("api_member_id"=>$memberid,
		                     "api_id"=>995,
		                     "api_furniture_type"=>5,
		                     "api_furniture_no"=>99,
		                     "api_furniture_id"=>995
		                     );
		$full_furn[] = array("api_member_id"=>$memberid,
		                     "api_id"=>998,
		                     "api_furniture_type"=>3,
		                     "api_furniture_no"=>98,
		                     "api_furniture_id"=>998
		                     );
		$req->response['api_furniture'] = $full_furn;
	} else if ($req->uri==="/kcsapi/api_port/port") {
		// Initialize user furniture if not set yet
		if (!$this->ruleFound) {
			$newrule = "\"api_furniture\":".json_encode($req->response['api_basic']['api_furniture']);
			$this->user->kcaccess[] = array("type"=>"PregReplace", "arg1"=>"\"api_furniture\":\\[(\\d+),(\\d+),(\\d+),(\\d+),(\\d+),(\\d+)\\]", "arg2"=>$newrule, "option"=>"");
			KCSql::inst()->update(array("kcaccess"=>utf8_encode(json_encode($this->user->kcaccess))),"forward_users")->where("memberid={$this->user->id}")->query();
		}
	} else if ($req->uri==="/kcsapi/api_start2") {
		$req->response["api_mst_furniture"][] = array(
			'api_id'=>990,
			'api_type'=>0,
			'api_no'=>99,
			'api_title'=>'Disabled',
			'api_description'=>'Disable Floor',
			'api_rarity'=>5,
			'api_price'=>0,
			'api_saleflag'=>0,
			'api_season'=>0
		);
		/*
		$req->response["api_mst_furniture"][] = array(
			'api_id'=>991,
			'api_type'=>1,
			'api_no'=>99,
			'api_title'=>'Disabled',
			'api_description'=>'Disable Wallpaper',
			'api_rarity'=>5,
			'api_price'=>0,
			'api_saleflag'=>0,
			'api_season'=>0
		);
		*/
		$req->response["api_mst_furniture"][] = array(
			'api_id'=>992,
			'api_type'=>2,
			'api_no'=>99,
			'api_title'=>'Disabled',
			'api_description'=>'Disable Window',
			'api_rarity'=>5,
			'api_price'=>0,
			'api_saleflag'=>0,
			'api_season'=>0
		);
		$req->response["api_mst_furniture"][] = array(
			'api_id'=>993,
			'api_type'=>3,
			'api_no'=>99,
			'api_title'=>'Disabled',
			'api_description'=>'Disable Decoration',
			'api_rarity'=>5,
			'api_price'=>0,
			'api_saleflag'=>0,
			'api_season'=>0
		);
		$req->response["api_mst_furniture"][] = array(
			'api_id'=>994,
			'api_type'=>4,
			'api_no'=>99,
			'api_title'=>'Disabled',
			'api_description'=>'Disable furniture',
			'api_rarity'=>5,
			'api_price'=>0,
			'api_saleflag'=>0,
			'api_season'=>0
		);
		$req->response["api_mst_furniture"][] = array(
			'api_id'=>995,
			'api_type'=>5,
			'api_no'=>99,
			'api_title'=>'Disabled',
			'api_description'=>'Disable Table',
			'api_rarity'=>5,
			'api_price'=>0,
			'api_saleflag'=>0,
			'api_season'=>0
		);
		$req->response["api_mst_furniture"][] = array(
			'api_id'=>999,
			'api_type'=>1,
			'api_no'=>99,
			'api_title'=>'Custom Background',
			'api_description'=>'Rewrite /kcs/resources/image/furniture/wall/100.png to your png(800x480)',
			'api_rarity'=>5,
			'api_price'=>0,
			'api_saleflag'=>0,
			'api_season'=>0
		);
		$req->response["api_mst_furniture"][] = array(
			'api_id'=>998,
			'api_type'=>3,
			'api_no'=>98,
			'api_title'=>'Animated Object',
			'api_description'=>'Swf furniture',
			'api_rarity'=>5,
			'api_price'=>0,
			'api_saleflag'=>0,
			'api_season'=>0
		);
		$req->response["api_mst_furnituregraph"][] = array(
			'api_id'=>998,
			'api_type'=>3,
			'api_no'=>98,
			'api_filename'=>'animated',
			'api_version'=>'1'
		);
	}
}

}
?>
