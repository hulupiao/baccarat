<?php
return array(
	'app_name' => array(
		'app', //the first app will be default run.
		'index'
	),
	'rule' => array(
		'dir/a/(\w+)/(\w+)/(\d+)' => array('namespace' => 'dir1', 'parameter' => array('id' => '3')),
	)
);
