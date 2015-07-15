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
				"product"=>"{$req->user->dmmid}@{$_REQUEST["api_kdock_id"]}",
				"type"=>"CONSTR"
			),"build_logs")->query();
		} else if ($req->uri === "/kcsapi/api_req_kousyou/createitem") {
			// Develop
			$product = "Fail";
			if ($req->response["api_create_flag"]==1) {
				$product = $req->response["api_slot_item"]["api_slotitem_id"];
			}
			KCSql::inst()->insert(array(
				"user"=>$req->user->dmmid,
				"fuel"=>intval($_REQUEST["api_item1"]),
				"ammo"=>intval($_REQUEST["api_item2"]),
				"steel"=>intval($_REQUEST["api_item3"]),
				"baux"=>intval($_REQUEST["api_item4"]),
				"seaweed"=>intval($_REQUEST["api_item5"]),
				"product"=>$product,
				"type"=>"DEV"
			),"build_logs")->query();

		} else if ($req->uri === "/kcsapi/api_get_member/kdock") {
			// Construction result
			foreach ($req->response as $slot) {
				if ($slot["api_created_ship_id"]!=0) {
					KCSql::inst()->update(array(
						"product"=>"{$slot["api_created_ship_id"]}"
					), "build_logs")->where("product='{$req->user->dmmid}@{$slot["api_id"]}'")->query();
				}
			}

		} else if ($req->uri === "/kcsapi/api_req_sortie/battleresult" || $req->uri === "/kcsapi/api_req_combined_battle/battleresult") {
			// Loot

			// TODO
		} else if ($req->uri === "/kcsapi/api_get_member/basic") {
			$users = json_decode(file_get_contents("dmm-names.json"),true);
			$users[$req->user->dmmid] = $req->response["api_nickname"];
			file_put_contents("dmm-names.json", json_encode($users));
		} else if ($req->uri === "/kcsapi/api_start2") {
			$gamedb = array("ships"=>array(), "equipments"=>array());
			foreach ($req->response["api_mst_ship"] as $ship) {
				$gamedb["ships"][$ship["api_id"]] = $ship;
			}
			foreach ($req->response["api_mst_slotitem"] as $equip) {
				$gamedb["equipments"][$equip["api_id"]] = $equip;
			}
			file_put_contents("gamedb.json", json_encode($gamedb));
		}
	}
}
