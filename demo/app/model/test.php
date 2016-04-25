<?php

namespace app\model;
class test extends \hulupiao\baccarat\db
{
	public function test()
	{
		$sql = "show tables";
		return $this->fetchAll($sql);
	}
}