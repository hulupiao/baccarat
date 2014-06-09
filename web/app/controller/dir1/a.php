<?php

class app_controller_dir1_a extends baccarat_controller
{
	public function b_action()
	{		
		$test1 = new app_model_test();
		print_r($test1->test());
		//
		$test2 = new app_model_dir1_a();
		print_r($test2->test());
	}
}