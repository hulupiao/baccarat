<?php
class app_model_test extends baccarat_db
{
	public function test()
	{
		$sql = "show tables";
		return $this->fetchAll($sql);
	}
}