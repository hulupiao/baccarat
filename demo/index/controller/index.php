<?php
namespace index\controller;
//namespace index\model;

class index extends \hulupiao\baccarat\controller
{
	public function index_action()
	{
		echo __METHOD__;
		$test_obj = new \index\model\test();
		$info = $test_obj->get();
		var_dump($info);
	}
}