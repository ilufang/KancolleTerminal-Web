<?php

require_once '../KCUser.class.php';

class KCViewer {
	private $user, $data, $update; // $user<KCUser>
	function __construct($user) {
		$this->user = $user;
		$this->readData();
	}
	function readData() {
		$data = KCSql::inst()->selectAll("game_data")->where("userid=".$this->user->id)->query();
		if (!$data || count($data)==0) {
			return false;
		}
		$this->data = json_decode($data[0]["gamedata"], true);
		$this->update = json_decode($data[0]["updates"], true);
		return sha1($data[0]["gamedata"]);
	}
	function writeData() {
		KCSql::inst()->update(array('gamedata'=>json_encode($this->data), 'updates'=>json_encode($this->update)), "game_data")->where("userid=".$this->user->id)->query();
	}

	private function set($key, $data) {
		if (!isset($data)) {
			return;
		}
		$this->data[$key] = $data;
		if (!in_array($key, $this->update)) $this->update[]=$key;
	}

	function afterRequest($req) {
		if ($req->errno != 1) {
			return;
		}
		switch ($req->uri) {
			case '/kcsapi/api_port/port':
				$this->set("api_basic", $req->response["api_basic"]);
				$this->set("api_ndock", $req->response["api_ndock"]);
				$this->set("api_material", $req->response["api_material"]);
				$this->set("api_deck", $req->response["api_deck_port"]);
				$this->set("api_ship", $req->response["api_ship"]);
				$this->set("api_combined_flag", $req->response["api_combined_flag"]);
				break;

			case '/kcsapi/api_get_member/basic':
				$this->set("api_basic", $req->response);
				break;

			case '/kcsapi/api_get_member/ndock':
				$this->set("api_ndock", $req->response);
				break;

			case '/kcsapi/api_get_member/kdock':
				$this->set("api_kdock", $req->response);
				break;

			case '/kcsapi/api_get_member/slot_item':
				$this->set("api_slot_item", $req->response);
				break;

			case '/kcsapi/api_get_member/useitem':
				$this->set("api_useitem", $req->response);
				break;

			case '/kcsapi/api_get_member/unsetslot':
				$this->set("api_slot_data", $req->response);
				break;

			case '/kcsapi/api_get_member/ship2':
				$this->set("api_ship", $req->response);
				break;

			case '/kcsapi/api_get_member/ship3':
				$this->set("api_deck", $req->response["api_deck_data"]);
				$this->set("api_slot_data", $req->response["api_slot_data"]);
				break;
		}
		// Prophet data
		switch ($req->uri) {
			case '/kcsapi/api_req_map/start':
			case '/kcsapi/api_req_map/next':
			case '/kcsapi/api_req_sortie/battle':
			case '/kcsapi/api_req_battle_midnight/sp_midnight':
			case '/kcsapi/api_req_sortie/airbattle':
			case '/kcsapi/api_req_battle_midnight/battle':
			case '/kcsapi/api_req_member/get_practice_enemyinfo':
			case '/kcsapi/api_req_practice/battle':
			case '/kcsapi/api_req_practice/midnight_battle':
			case '/kcsapi/api_req_practice/battle_result':
			case '/kcsapi/api_req_sortie/battleresult':
			case '/kcsapi/api_port/port':
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
				$prophetReq['detail']['postBody']['api_token'] = '0000000000000000000000000000000000000000';
				$this->set("battle", $prophetReq);
				break;
		}
		$this->writeData();
	}
}


if (!isset($_REQUEST["api_token"])) {
	echo "200 OK";
}
