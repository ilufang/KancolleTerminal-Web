<?php
/**
 *	KCResource
 *
 *	Integrated handler for requesting kancolle static assets
 *
 *	2015 by ilufang
 */

require_once '../KCForwardUser.class.php';

class KCResource {

// Initialize at construction
private $uri,$resource_url,$meta_url,$version;
// Initialize on demand (lazy)
private $meta, $data, $errno;

private $user;

/**
 *	download
 *
 *	Download the resource from remote server and save meta information
 *	Writes $meta and $data
 *
 */
function download() {
	//file_put_contents("download.log", file_get_contents("download.log").$_SERVER["REQUEST_URI"]."\n");

	// Make local directories
	mkdir(dirname($this->resource_url),0777,true);
	mkdir(dirname($this->meta_url),0777,true);

	// Download file
	$remote_url = "http://125.6.187.253".$this->uri;
	$this->data = file_get_contents($remote_url);
	if (strlen($this->data)==0) {
		unlink($this->resource_url);
		unlink($this->meta_url);
		$this->errno = 404;
	}
	file_put_contents($this->resource_url, $this->data);

	// Generate meta
	$this->meta = array("checksum" => sha1($this->data), "version" => $this->version);
	file_put_contents($this->meta_url, json_encode($this->meta));
}

/**
 *	compareVersion
 *
 *	Compare stored version with the requested. Download again if the stored file is out-dated.
 *	Only call in getMeta as direct access to instance variable is used to prevent looped call
 */
function compareVersion() {
	if (!$this->version) {
		return;
	}
	if (strcasecmp($this->version, $this->meta["version"])!=0) {
		file_put_contents("download.log", file_get_contents("download.log").$this->resource_url.": Version outdated ('{$this->version}' against '{$this->meta["version"]}')\n");
		$this->download();
	}
}

/**
 *	getMeta
 *
 *	Get resource meta.
 *	Verify version validity in the process.
 *	Lazy initialization
 *
 *	@return Meta array
 */
function getMeta() {
	if ($this->meta) {
		return $this->meta;
	}
	// Initialize
	if (file_exists($this->meta_url) ) {
		// Read from existing file
		//header("MetaPhase: 0");
		$this->meta = json_decode(file_get_contents($this->meta_url),true);
		$this->compareVersion();
	} else {
		//header("MetaPhase: 1");
		if (!file_exists($this->resource_url)) {
			$this->download();
			file_put_contents("download.log", file_get_contents("download.log")."File not found:".$this->meta_url."\n");
		} else {
			$this->data = file_get_contents($this->resource_url);
			header("Checksum: ".sha1($this->data));
			$this->meta = array('checksum' => sha1($this->data), 'version' => 'custom');
		}
	}

	return $this->meta;
}

/**
 *	getData
 *
 *	Get resource.
 *	Lazy initialization
 *
 *	@return Data
 */
function getData() {
	if ($this->data) {
		return $this->data;
	}
	// Initialize
	if (file_exists($this->resource_url)) {
		// Read from existing file
		$this->data = file_get_contents($this->resource_url);
		//header("SHA-actual: ".sha1($this->data));
		//header("SHA-record: ".$this->getMeta()['checksum']);
		if (strlen($this->data)==0) {
			$this->download();
			file_put_contents("download.log", file_get_contents("download.log").$this->resource_url.": Empty data"."\n");
		} else if (strcasecmp(sha1($this->data), $this->getMeta()['checksum']) != 0) {
			$this->download();
			file_put_contents("download.log", file_get_contents("download.log").$this->resource_url.": Corrupt data "."\n");
		}
	} else {
		$this->download();
		file_put_contents("download.log", file_get_contents("download.log").$this->resource_url.": Data not found"."\n");
	}
	return $this->data;
}

/**
 *	Constructor
 *
 *	Parse the request uri and generate filesystem urls
 *	Download the file if it does not exist
 *
 *	@param filename: Requesting file, starting with "/kcs/"
 *	@param version: File version string
 */
function __construct($filename,$version) {
	// User session
	$this->user = new KCUser();
	// Try get user info
	if (!$this->user->initWithSession()) {
		if (isset($_REQUEST["api_token"])) {
			$this->user->initWithToken($_REQUEST["api_token"]);
		} else if (isset($_COOKIE["username"])) {
			$this->user->initWithAuth($_COOKIE["username"], $_COOKIE["passhash"]);
		}
	}

	if ($this->user->init_status) {
		if ($this->user->gamemode==3) {
			$this->user = new KCForwardUser($this->user);
			header("KC-User: ".$this->user->dmmid);
			if (isset($this->user->kcaccess)) {
				$cond_subject = " ";
				$cond_rule = "(.*)";

				foreach ($this->user->kcaccess as $entry) {
					$disabled = false;
					foreach (explode(",", $entry['option']) as $option) {
						switch ($option) {
							case "!":
								$disabled = true;
						}
					}
					if ($disabled) {
						continue;
					}
					$entry["arg1"] = str_ireplace("%{REQUEST_URI}", $filename, $entry["arg1"]);
					$entry["arg1"] = str_ireplace("/", "\\/", $entry["arg1"]);
					$entry["arg2"] = str_ireplace("%{REQUEST_URI}", $filename, $entry["arg2"]);
					switch ($entry["type"]) {
						case 'RewriteRule':
							if (preg_match("/$cond_rule/", $cond_subject)!=0) {
								$filename = preg_replace("/$entry[arg1]/", $entry["arg2"], $filename);
								header("KC-Rewrite: $filename");
							}
							$cond_subject = " ";
							$cond_rule = "(.*)";
							break;
						case 'RewriteCond':
							$cond_subject = $entry["arg1"];
							$cond_rule = $entry["arg2"];
							break;
					}
				}
	// End kcaccess
			}
		}

	} else {
		$this->user = false;
	}

	// Initialize member variables
	$this->uri = $filename;
	$filepath = strtok($filename,"?");
	$this->resource_url = "files".strtok($filename,"?");
	$this->meta_url = "meta".strtok($filename,"?").".json";

	if ($this->user) {
		foreach ($this->user->respacks as $pack) {
			$pack = trim($pack);
			if (strlen($pack)>0) {
				if (file_exists("resource_packs/".$pack.$filepath)) {
					$this->data = file_get_contents("resource_packs/".$pack.$filepath);
					$this->meta = array("checksum"=>sha1($this->data), "version"=>"sideloaded");
					break;
				}
			}
		}
	}
	$this->version = $version;
	$this->errno = 100;
}

function init() {
	$this->getMeta();
	$this->getData();
	if ($this->errno==404) {
		return FALSE;
	}
	return TRUE;
}

/**
 * printResponse
 *
 * Sends the resource to output. (Interface entry point)
 *
 */
function printResponse() {

	// MIME
	$fileinfo = new SplFileInfo(strtok($this->uri,"?"));
	switch ($fileinfo->getExtension()) {
		case "swf":
			header("Content-Type: application/x-shockwave-flash");
			break;
		case "jpg":
		case "jpeg":
			header("Content-Type: image/jpeg");
			break;
		case "png":
			header("Content-Type: image/png");
			break;
		case "gif":
			header("Content-Type: image/gif");
			break;
		case "mp3":
			header("Content-Type: audio/mpeg");
			break;
		default:
			header("Content-Type: text/plain");
			break;
	}

	// Check if 304
	$etag = $this->getMeta()['checksum'];
	if (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"]==="\"$etag\"") {
		header("HTTP/1.1 304 Not Modified");
		return;
	}

	// Cache
	header("Cache-Control: public, must-revalidate");
	header("Etag: \"$etag\"");

	// Data
	header("Content-Length: ".strlen($this->getData()));
	echo $this->getData();
}

}
