<?php
namespace Test\Base;

use \DbMockLibrary\Base;

class InitTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        Base::init();

        // prepare
        $reflection = new \ReflectionClass('\DbMockLibrary\Base');
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertInstanceOf('\DbMockLibrary\Base', $staticProperties['instance']);
    }
}