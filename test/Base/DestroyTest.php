<?php
namespace DbMockLibrary\Test\Base;

use DbMockLibrary\Base;
use DbMockLibrary\Test\TestCase;

class DestroyTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        Base::init();
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