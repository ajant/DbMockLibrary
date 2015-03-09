<?php
namespace Test\MockMethodCalls;

use \DbMockLibrary\MockMethodCalls;

class GetFullTraceDetailsXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Invalid method');
        MockMethodCalls::init(['collection' => []]);
        $reflection = new \ReflectionClass(MockMethodCalls::getInstance());
        $validateCollectionsMethod = $reflection->getMethod('getFullTraceDetails');
        $validateCollectionsMethod->setAccessible(true);

        // invoke logic & test
        $validateCollectionsMethod->invoke(MockMethodCalls::getInstance(), new \stdClass(), 'fooBar');
    }
}