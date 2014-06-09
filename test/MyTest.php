<?php
//require_once "PHPUnit/Framework/TestCase.php";
 
class MyTest extends PHPUnit_Framework_TestCase
{
    public function testCalculate()
    {
        $this->assertEquals(2, 1 + 1);
    }
}
?>