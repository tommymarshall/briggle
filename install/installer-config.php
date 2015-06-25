<?php

$setting = array(

	'config'		=>	array(
		'folder'	=>	'../bc-includes/',
		'file'		=>	'configuration.php'
		),

	'database'		=>	array(
		'name'		=> 'DB_NAME',
		'user'		=> 'DB_USER',
		'pass'		=> 'DB_PASS',
		'host'		=> 'DB_HOST'
		),

	'requirements'	=>	array(
		'php'		=>	'5.2'
		),

	'name'			=>	'Briggle',
	'version'		=>	'1.0',
	'finished'		=>	'Login Now',
	'after_install'	=>	'../'
	);

function connect()
 {
	$link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
	mysql_select_db(DB_NAME,$link);
 }

?>
