<?php
/*
 *	pythonLogin
 *
 *	Invokes kancolle.tv's python login script to get game url
 *
 *	WARNING: DEPRECATED. USE DMMLOGIN.CLASS.PHP INSTEAD
 */
function pyDMMLogin($username, $password) {
	$command = escapeshellcmd("/usr/local/bin/python3.3 /var/www/kancolle/dmmauth/kclib/kc.py $username $password");
	$output = shell_exec($command);
//	echo $output;
	return json_decode($output, true);
}
