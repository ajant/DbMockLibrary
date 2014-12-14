<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class RecordTraceTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        MockLibrary::init([]);
        $reflection = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $traceProperty = $reflection->getProperty('traces');
        $traceProperty->setAccessible(true);

        // test
        MockLibrary::getInstance()->recordTrace();

        // prepare
        $trace = $traceProperty->getValue(MockLibrary::getInstance());

        // test
        $this->assertArraySubset(['function' => 'test_function', 'class' => 'Test\MockLibrary\RecordTraceTest'], $trace[0][0]);
    }
}