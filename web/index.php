<?php
//@todo
print_r($_GET);exit;
///////////
require 'config.php';

require '../baccarat/init.php';

$_config = array(
	'db' => require ROOT_PATH.'config.db.php',
	'router' => require ROOT_PATH.'config.router.php'
);

baccarat_init::run($_config);