<?php
/**
 *	viewer class
 *
 *	Keeps track of the player's status (game_data table)
 *
 *	2015 by ilufang
 */
require_once '../KCUser.class.php';

class KCViewer {
	private $user, $data, $update; // $user<KCUser>
	function __construct($user) {
		$this->user = $user;
		$this->readData();
	}
	function readData() {
		$saveprofile = KCSql::inst()->selectAll("game_data")->where("userid=".$this->user->id)->query();
		if (!$saveprofile || count($saveprofile)==0) {
			return false;
		}
		$this->data = json_decode($saveprofile[0]["gamedata"], true);
		$this->update = json_decode($saveprofile[0]["updates"], true);
		return sha1($saveprofile[0]["gamedata"]);
	}
	function writeData() {
		KCSql::inst()->update(array('gamedata'=>json_encode($this->data), 'updates'=>json_encode($this->update)), "game_data")->where("userid=".$this->user->id)->query();
	}

	private function set($key, $value) {
		$this->data[$key] = $value;
		if (!in_array($key, $this->update)) $this->update[]=$key;
	}

	function beforeRequest($req) {
		date_default_timezone_set("Asia/Shanghai"); // TODO
		switch ($req->uri) {
			case '/kcsapi/api_port/port':
				if (isset($this->data["suppress_port"]) && $this->data["suppress_port"]) {
					$req->response = $this->data["api_port"];
					$req->errno = 1;
					header("KC-Intercept: port");
					$req->req_type="WRITTEN";
				} else {
					header("KC-Intercept: none");
				}
				break;
			case '/kcsapi/api_terminal/suppress_port/suppress':
				$this->set("suppress_port", 1);
				$this->writeData();
				$req->response = "Port将被拦截, 没有数据会经过Kadokawa服务器.(".date("H:i:s").")";
				$req->errno = 1;
				$req->req_type="WRITTEN";
				break;
			case '/kcsapi/api_terminal/suppress_port/release':
				$this->set("suppress_port", 0);
				$this->writeData();
				$req->response = "Port将正常请求.(".date("H:i:s").")";
				$req->errno = 1;
				$req->req_type="WRITTEN";
				break;
		}
	}

	function afterRequest($req) {
		if ($req->errno != 1) {
			return;
		}
		switch ($req->uri) {
			case '/kcsapi/api_port/port':
				// Assign mode: do not overwrite
				// TODO: potential glitch: api_combined_status may be not erased after event end update
				if (!isset($this->data["api_port"])) {
					$this->data["api_port"] = array();
				}
				foreach ($req->response as $key => $value) {
					$this->data["api_port"][$key] = $value;
				}
				break;
			case '/kcsapi/api_get_member/slot_item':
				if (!isset($this->data["api_port"])) {
					$this->data["api_port"] = array();
				}
				$this->data["api_port"]["api_slot_item"] = $req->response;
				$this->set("api_slot_item", $req->response);

				break;

			case '/kcsapi/api_get_member/basic':
				$this->set("api_basic", $req->response);
				break;
/*
			case '/kcsapi/api_get_member/ndock':
				$this->set("api_ndock", $req->response);
				break;
*/
			case '/kcsapi/api_get_member/kdock':
				$this->set("api_kdock", $req->response);
				break;

			case '/kcsapi/api_get_member/useitem':
				$this->set("api_useitem", $req->response);
				break;

			case '/kcsapi/api_get_member/unsetslot':
				$this->set("api_slot_data", $req->response);
				break;
/*
			case '/kcsapi/api_get_member/ship2':
				$this->set("api_ship", $req->response);
				break;

			case '/kcsapi/api_get_member/ship3':
				$this->set("api_deck", $req->response["api_deck_data"]);
				$this->set("api_slot_data", $req->response["api_slot_data"]);
				break;
				*/
		}
		// Prophet data
		switch ($req->uri) {
			case '/kcsapi/api_req_map/start':
			case '/kcsapi/api_req_map/next':
			case "/kcsapi/api_req_combined_battle/airbattle":
			case "/kcsapi/api_req_combined_battle/battle":
			case "/kcsapi/api_req_combined_battle/midnight_battle":
			case "/kcsapi/api_req_combined_battle/sp_midnight":
			case "/kcsapi/api_req_combined_battle/battle_water":
			case "/kcsapi/api_req_combined_battle/battleresult":
			case '/kcsapi/api_req_sortie/battle':
			case '/kcsapi/api_req_battle_midnight/sp_midnight':
			case '/kcsapi/api_req_sortie/airbattle':
			case '/kcsapi/api_req_battle_midnight/battle':
			case '/kcsapi/api_req_member/get_practice_enemyinfo':
			case '/kcsapi/api_req_practice/battle':
			case '/kcsapi/api_req_practice/midnight_battle':
			case '/kcsapi/api_req_practice/battle_result':
			case '/kcsapi/api_req_sortie/battleresult':
			case '/kcsapi/api_get_member/kdock':
			case '/kcsapi/api_req_kousyou/createitem':
			case '/kcsapi/api_req_kousyou/getship':
			case '/kcsapi/api_req_kousyou/destroyitem2':
			case '/kcsapi/api_req_kousyou/destroyship':
			case '/kcsapi/api_req_kousyou/remodel_slot':
			case '/kcsapi/api_req_kaisou/powerup':
			case '/kcsapi/api_req_kaisou/slotset':
			case '/kcsapi/api_req_kaisou/unsetall':
			case '/kcsapi/api_req_hensei/change':
			case '/kcsapi/api_req_hensei/preset_select':
			case '/kcsapi/api_req_hokyu/charge':
			case '/kcsapi/api_req_kaisou/slot_exchange_index':
			case '/kcsapi/api_req_nyukyo/speedchange':
			case '/kcsapi/api_req_nyukyo/start':
			case '/kcsapi/api_get_member/ship3':
				$prophetReq = array(
					'status' => 200,
					'checksum' => date('H:i:s'),
					'detail' => array (
						'path' => $req->uri,
						'method' => 'POST',
						'body' => $req->response,
						'postBody' => $req->post
					)
				);
				$prophetReq['detail']['postBody']['api_token'] = substr($prophetReq['detail']['postBody']['api_token'], 0, 8);
				$this->set("battle", $prophetReq);
				break;
			case '/kcsapi/api_port/port':
			case '/kcsapi/api_get_member/slot_item':
				// Port integration: returned port data contains slotitem
				if (isset($this->data["api_port"])) {
					$prophetReq = array(
						'status' => 200,
						'checksum' => date('H:i:s'),
						'detail' => array(
							'path' => "/kcsapi/api_port/port",
							'method' => 'POST',
							'body' => $this->data["api_port"],
							'postBody' => array()
						)
					);
					$this->set("battle", $prophetReq);
				}
				break;
		}
		$this->writeData();
	}
}
