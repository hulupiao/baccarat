<?php
namespace app\controller\dir1;
class a extends hulupiao\baccarat\controller
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