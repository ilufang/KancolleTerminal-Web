<?php
/**
 *	KCResource
 *
 *	Integrated handler for requesting kancolle static assets
 *
 *	2015 by ilufang
 */


class KCResource {

// Initialize at construction
private $uri,$resource_url,$meta_url,$version;
// Initialize on demand (lazy)
private $meta, $data;

/**
 *	download
 *
 *	Download the resource from remote server and save meta information
 *	Writes $meta and $data
 *
 */
function download() {
	// Make local directories
	mkdir(dirname($this->resource_url),0777,true);
	mkdir(dirname($this->meta_url),0777,true);

	// Download file
	$remote_url = "http://125.6.188.25".$this->uri;
	$this->data = file_get_contents($remote_url);
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
	if (version_compare($this->version, $this->meta["version"], ">")) {
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
	if (file_exists($this->meta_url)) {
		// Read from existing file
		$this->meta = json_decode(file_get_contents($this->meta_url),true);
		$this->compareVersion();
	} else {
		$this->download();
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
	} else {
		$this->download();
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
	// Initialize member variables
	$this->uri = $filename;
	$this->resource_url = "files".strtok($filename,"?");
	$this->meta_url = "meta".strtok($filename,"?").".json";
	$this->version = $version;
	// Nullify uninitialized
	$this->meta = false;
	$this->data = false;
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
	if ($_SERVER["HTTP_IF_NONE_MATCH"]==="\"$etag\"") {
		header("HTTP/1.1 304 Not Modified");
		return;
	}

	// Cache
	header("Cache-Control: public, must-revalidate");
	header("Etag: \"$etag\"");

	// Data
	echo $this->getData();
}

}