<?php
/**
 *	KCSql
 *
 *	Mysql shortcuts
 *
 *	2015 by ilufang
 */

require_once "config.php";

class KCSql{

private $con = false;
private $sql = "";

private static $instance = false;

/**
 *	inst
 *
 *	Return the internally-kept "only" session instance for operation
 */
public static function inst() {
	if (!self::$instance) {
		self::$instance = new KCSql();
	}
	return self::$instance;
}

/**
 *	error
 *
 *	Get error info
 *
 *	@return $con->error
 */
public function error(){
	return $this->con->error;
}

/**
 *	insertId
 *
 *	Last insert id
 */
public function insertId() {
	return $this->con->insert_id;
}

/**
 *	query - Manual
 *
 *	Make query and return result as an array of associative arrays
 */
public function querySql($sql) {
	file_put_contents("sql.txt", $sql);

	global $config;

	if (!$this->con) {
		$this->con = new mysqli($config["mysql_host"],$config["mysql_user"],$config["mysql_pswd"],$config["mysql_db"]);
		if ($this->con->connect_errno) {
			return false;
		}
		$this->con->set_charset("utf8");
	}


	$result = $this->con->query($sql);

	if (!$result) {
		// For failed query
		return false;
	}

	if ($result->num_rows == 0) {
		// For empty response
		return true;
	}

	$ret = array();
	while($row = $result->fetch_assoc()) {
		$ret[] = $row;
	}
	return $ret;
}

/**
 *	query - Automatic
 *
 *	Make query with cached sql statement
 */


public function query() {
	file_put_contents("sql.txt", $this->sql);
	return $this->querySql($this->sql);
}

/**
 *	sqlarr
 *
 *	Convert array to sql string
 *	Implode with comma, and wrap strings with ''
 */
public function sqlarr($arr) {
	foreach ($arr as $key => &$value) {
		if (is_string($value)) {
			$value = $this->con->escape_string($value);
			$value="'$value'";
		}
	}
	return implode(",", $arr);
}


// Major actions

// Select
public function select($columes, $table) {
	global $config;
	$this->sql = "SELECT ".implode(",",$columes)." FROM $config[mysql_prefix]_$table\n";
	return $this;
}

public function selectAll($table) {
	global $config;
	$this->sql = "SELECT * FROM $config[mysql_prefix]_$table\n";
	return $this;
}

// Insert Into
public function insert($data, $table) {
	global $config;
	$columes = array();
	$values = array();
	foreach ($data as $key => &$value) {
		$columes[] = $key;
		$values[] = $value;
	}
	$this->sql = "INSERT INTO $config[mysql_prefix]_$table\n (".implode(",", $columes).")";
	$this->sql .= "VALUES (".$this->sqlarr($values).")\n";
	return $this;
}

// Update
public function update($data, $table) {
	global $config;
	$this->sql = "UPDATE $config[mysql_prefix]_$table\n";
	$entries = array();
	foreach ($data as $key => &$value) {
		if (is_string($value)) {
			$value = $this->con->escape_string($value);
			$value = "'$value'";
		}
		$entries[] = "$key=$value";
	}
	$this->sql .= "SET ".implode(",", $entries)."\n";
	return $this;
}

// Delete
public function delete($table) {
	global $config;
	$this->sql = "DELETE FROM $config[mysql_prefix]_$table\n";
	return $this;
}

// Secondary constrains

// Where
public function where($criteria) {
	$this->sql .= "WHERE $criteria\n";
	return $this;
}

/**
 *	createDB
 *
 *	Initialize everything
 */
public static function createDB() {
	global $config;

	$con = new mysqli($config["mysql_host"],$config["mysql_user"],$config["mysql_pswd"],$config["mysql_db"]);
	if ($con->connect_errno) {
		echo $con->connect_error;
		return false;
	}

	// Basic user info
	$sql = "CREATE TABLE $config[mysql_prefix]_hub_users (
		memberid	INT AUTO_INCREMENT PRIMARY KEY,
		username	VARCHAR(64),
		password	VARCHAR(40),
		gamemode	INT,
		token		VARCHAR(40)
	)";
	$con->query($sql);

	// Forwarding user info
	$sql = "CREATE TABLE $config[mysql_prefix]_forward_users (
		memberid	INT PRIMARY KEY,
		dmmid		INT,
		token		VARCHAR(40),
		starttime	BIGINT,
		serveraddr	VARCHAR(16),
		lastupdate	VARCHAR(64)
	)";
	$con->query($sql);

}
	
}

