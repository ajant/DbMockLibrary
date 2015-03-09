<?php
namespace Test\Base;

use \DbMockLibrary\Base;

class DestroyTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        Base::init([]);
        $reflection = new \ReflectionClass('\DbMockLibrary\Base');
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertInstanceOf('\DbMockLibrary\Base', $staticProperties['instance']);

        // invoke logic
        Base::getInstance()->destroy();

        // prepare
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertNull($staticProperties['instance']);
    }
}