<?php
/*
	Warning: This is a workaround for the login issue
	This file contains code that is not redistributable.
	If you insist to migrate this login script to another server, please make sure relevant python environment is configured accordingly
 */
function pyDMMLogin($username, $password) {
	$command = escapeshellcmd("/opt/python3.3/bin/python3.3 /var/www/kancolle/dmmauth/kclib/kc.py $username $password");
	$output = shell_exec($command);
//	echo $output;
	return json_decode($output, true);
}
