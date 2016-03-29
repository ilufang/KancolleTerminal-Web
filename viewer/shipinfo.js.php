<?php
header("Content-Type: application/javascript; charset=utf-8");
require_once 'agectrl.php';
tryModified("i18n_shipinfo.json");
?>

Object.assign(i18nDB, <?php
	echo file_get_contents("i18n_shipinfo.json");
?>);

<?php
require 'shipinfo/index.js.php';
