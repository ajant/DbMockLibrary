<?php
namespace DbMockLibrary\Test\MockMethodCalls;

use DbMockLibrary\MockMethodCalls;
use DbMockLibrary\Test\TestCase;

class RecordArgumentsTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        MockMethodCalls::init();

        // prepare
        $reflection = new \ReflectionClass('\DbMockLibrary\MockMethodCalls');
        $callArgumentsProperty = $reflection->getProperty('callArguments');
        $callArgumentsProperty->setAccessible(true);

        // test
        MockMethodCalls::getInstance()->recordArguments(new \Exception(), 'getMessage', ['bar']);

        // test
        $this->assertEquals([['Exception::getMessage' => ['bar']]], $callArgumentsProperty->getValue(MockMethodCalls::getInstance()));
    }
}