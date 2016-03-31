<?php
/**
 *	Ban
 *
 *	Denies a user if he/she provoked other server members
 *
 *	2015 by ilufang
 */
class KCBan {
	private $db, $dbfile;
	function __construct($banfile) {
		$this->dbfile = $banfile;
		$this->db = json_decode(file_get_contents($banfile), true);
	}

	private function updateVotes($user) {
		if (isset($this->db[$user])) {
			$votelist = $this->db[$user];
			foreach ($votelist as $voteip => $endtime) {
				if ($endtime < time()) {
					unset($votelist[$voteip]);
				}
			}
			if (count($votelist)==0) {
				unset($this->db[$user]);
				return false;
			}
			return true;
		}
		return false;
	}

	public function isBanned($user) {
		$this->updateVotes($user);
		if (!isset($this->db[$user])) {
			return false;
		}
		if (count($this->db[$user])>=3) {
			return true;
		} else {
			return false;
		}
	}

	public function banlist() {
		$result = array();
		foreach ($this->db as $user => $votelist) {
			if ($this->updateVotes($user)) {
				$result[$user] = array();
				foreach ($votelist as $voteip => $endtime) {
					$result[$user][$voteip] = $endtime-time();
				}
			}
		}
		$this->save();
		return $result;
	}

	public function vote($user) {
		$voteip = sha1($_SERVER["REMOTE_ADDR"]);
		$voteip = substr($voteip, -8);
		if (!isset($this->db[$user])) {
			$this->db[$user] = array();
		}
		$this->db[$user][$voteip]=0;
		$actime = time()+60*60*12;
		if (count($this->db[$user])>3)
		$actime += (count($this->db[$user])-3)*60*60*12;
		$this->db[$user][$voteip] = $actime;


		$this->save();
		return $voteip;
	}

	public function save() {
		file_put_contents($this->dbfile, json_encode($this->db));
	}


}
