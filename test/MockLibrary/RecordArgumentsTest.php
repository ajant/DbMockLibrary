<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class RecordArgumentsTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        MockLibrary::init([]);

        // prepare
        $reflection = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $callArgumentsProperty = $reflection->getProperty('callArguments');
        $callArgumentsProperty->setAccessible(true);

        // test
        MockLibrary::getInstance()->recordArguments(new \Exception(), 'getMessage', ['bar']);

        // test
        $this->assertEquals([['Exception::getMessage' => ['bar']]], $callArgumentsProperty->getValue(MockLibrary::getInstance()));
    }
}