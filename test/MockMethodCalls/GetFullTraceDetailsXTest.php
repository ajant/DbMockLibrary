<?php
namespace DbMockLibrary\Test\MockMethodCalls;

use DbMockLibrary\MockMethodCalls;
use DbMockLibrary\Test\TestCase;

class GetFullTraceDetailsXTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Invalid method');
        MockMethodCalls::init();
        $reflection = new \ReflectionClass(MockMethodCalls::getInstance());
        $validateCollectionsMethod = $reflection->getMethod('getFullTraceDetails');
        $validateCollectionsMethod->setAccessible(true);

        // invoke logic & test
        $validateCollectionsMethod->invoke(MockMethodCalls::getInstance(), new \stdClass(), 'fooBar');
    }
}