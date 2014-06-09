<?php
class MultipleDependenciesTest extends PHPUnit_Framework_TestCase
{
    public function testProducerFirst()
    {
        $this->assertTrue(true);
        return 'first';
    }
 
    public function testProducerSecond()
    {
        $this->assertTrue(true);
        return 'second';
    }
 
    /**
     * @depends testProducerFirst
     * @depends testProducerSecond
     */
    public function testConsumer()
    {
        $this->assertEquals(
            array('first', 'second'),
            func_get_args()
        );
    }
}
?>