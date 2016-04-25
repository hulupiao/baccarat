<?php
//@todo

use hulupiao\baccarat;
///////////

require '../vendor/autoload.php';

require 'config.php';

require '../baccarat/baccarat.php';

$_config = array(
	'db' => require ROOT_PATH.'config.db.php',
	'router' => require ROOT_PATH.'config.router.php'
);

hulupiao\baccarat\baccarat::run($_config);