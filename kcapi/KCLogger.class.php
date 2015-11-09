<?php
/**
 *	KCLogger
 *
 *	Construction, Development & Loot request post-processor.
 *	Log entries to mysql
 *
 *	2015 by ilufang
 */


class KCLogger {
	static function request($req) {
		if ($req->uri === "/kcsapi/api_req_kousyou/createship") {
			// Construct Request
			KCSql::inst()->insert(array(
				"user"=>$req->user->dmmid,
				"fuel"=>intval($_REQUEST["api_item1"]),
				"ammo"=>intval($_REQUEST["api_item2"]),
				"steel"=>intval($_REQUEST["api_item3"]),
				"baux"=>intval($_REQUEST["api_item4"]),
				"seaweed"=>intval($_REQUEST["api_item5"]),
				"product"=>($req->user->dmmid)*10+$_REQUEST["api_kdock_id"],
				"type"=>"CONSTR"
			),"build_logs")->query();
		} else if ($req->uri === "/kcsapi/api_req_kousyou/createitem") {
			// Develop
			$product = -1;
			if ($req->response["api_create_flag"]==1) {
				$product = $req->response["api_slot_item"]["api_slotitem_id"];
			}
			KCSql::inst()->insert(array(
				"user"=>$req->user->dmmid,
				"fuel"=>intval($_REQUEST["api_item1"]),
				"ammo"=>intval($_REQUEST["api_item2"]),
				"steel"=>intval($_REQUEST["api_item3"]),
				"baux"=>intval($_REQUEST["api_item4"]),
				"product"=>$product,
				"type"=>"DEV"
			),"build_logs")->query();

		} else if ($req->uri === "/kcsapi/api_get_member/kdock") {
			// Construction result
			foreach ($req->response as $slot) {
				if ($slot["api_created_ship_id"]!=0) {
					$slot_identifier = ($req->user->dmmid)*10+$slot['api_id'];
					KCSql::inst()->update(array(
						"product"=>"{$slot["api_created_ship_id"]}"
					), "build_logs")->where("product=$slot_identifier")->query();
				}
			}

		} else if ($req->uri === "/kcsapi/api_req_map/start" || $req->uri === "/kcsapi/api_req_map/next") {
			KCSql::inst()->delete("build_logs")->where("product=-2 AND type='LOOT' AND user=".$req->user->dmmid)->query();
			KCSql::inst()->insert(array(
				"user"=>$req->user->dmmid,
				"fuel"=>$req->response["api_maparea_id"],
				"ammo"=>$req->response["api_mapinfo_no"],
				"steel"=>$req->response["api_no"],
				"baux"=>$req->response["api_event_id"],
				"seaweed"=>27,
				"product"=>-2,
				"type"=>"LOOT"
			), "build_logs")->query();
		} else if ($req->uri === "/kcsapi/api_req_sortie/battleresult" || $req->uri === "/kcsapi/api_req_combined_battle/battleresult") {
			// Loot
			if (isset($req->response["api_get_ship"]) && isset($req->response["api_get_ship"]["api_ship_id"])) {
				$rank = array("S"=>0,"A"=>1,"B"=>2,"C"=>3,"D"=>4,"E"=>5);
				KCSql::inst()->update(array("product"=>$req->response["api_get_ship"]["api_ship_id"],"seaweed"=>$rank[$req->response["api_win_rank"]]), "build_logs")->where("user=".$req->user->dmmid." AND product=-2 AND type='LOOT'")->query();
			} else {
				KCSql::inst()->delete("build_logs")->where("product=-2 AND type='LOOT' AND user=".$req->user->dmmid)->query();
			}
		} else if ($req->uri === "/kcsapi/api_get_member/basic") {
			$users = json_decode(file_get_contents("dmm-names.json"),true);
			$users[$req->user->dmmid] = $req->response["api_nickname"];
			$users["20186645"] = "test";
			file_put_contents("dmm-names.json", json_encode($users));
		} else if ($req->uri === "/kcsapi/api_start2") {
			$gamedb = array("ships"=>array(), "equipments"=>array());
			foreach ($req->response["api_mst_ship"] as $ship) {
				$gamedb["ships"][$ship["api_id"]] = $ship;
			}
			$gamedb["ships"][-2] = array("api_name"=>"正在战斗...");
			foreach ($req->response["api_mst_slotitem"] as $equip) {
				$gamedb["equipments"][$equip["api_id"]] = $equip;
			}
			$gamedb["equipments"][-1] = array("api_name"=>"Fail");
			foreach ($req->response["api_mst_furniture"] as $furniture) {
				$gamedb["furniture"][$furniture["api_id"]] = $furniture;
			}
			foreach ($req->response["api_mst_shipgraph"] as $shipres) {
				$gamedb["ships"][$shipres["api_id"]]["api_filename"] = $shipres["api_filename"];
			}
			file_put_contents("gamedb.json", json_encode($gamedb));
		}
	}
}
