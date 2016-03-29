<?php
header("Content-Type: application/javascript; charset=utf-8");
require_once 'agectrl.php';
tryModified("i18n_iteminfo.json");
?>

Object.assign(i18nDB, <?php
	echo file_get_contents("i18n_iteminfo.json");
?>);

var ItemInfoArea = <?php require 'iteminfo/index.js.php';?>
