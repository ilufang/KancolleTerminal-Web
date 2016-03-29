<?php
header("Content-Type: application/javascript; charset=utf-8");
require 'agectrl.php';
tryModified("i18n_master.json")
?>
Object.assign(i18nDB, <?php echo file_get_contents("i18n_master.json");?>);
var MainView = <?php require 'dashboard/index.js.php';?>
