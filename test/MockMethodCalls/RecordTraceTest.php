<?php
namespace Test\MockMethodCalls;

use \DbMockLibrary\MockMethodCalls;

class RecordTraceTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        MockMethodCalls::init([]);
        $reflection = new \ReflectionClass('\DbMockLibrary\MockMethodCalls');
        $traceProperty = $reflection->getProperty('traces');
        $traceProperty->setAccessible(true);

        // test
        MockMethodCalls::getInstance()->recordTrace();

        // prepare
        $trace = $traceProperty->getValue(MockMethodCalls::getInstance());

        // test
        $this->assertArraySubset(['function' => 'test_function', 'class' => 'Test\MockMethodCalls\RecordTraceTest'], $trace[0][0]);
    }
}