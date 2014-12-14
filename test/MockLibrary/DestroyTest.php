<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class DestroyTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        MockLibrary::init([]);
        $reflection = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertInstanceOf('\DbMockLibrary\MockLibrary', $staticProperties['instance']);

        // invoke logic
        MockLibrary::getInstance()->destroy();

        // prepare
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertNull($staticProperties['instance']);
    }
}