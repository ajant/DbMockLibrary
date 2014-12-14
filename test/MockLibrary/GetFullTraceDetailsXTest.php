<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class GetFullTraceDetailsXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Invalid method');
        MockLibrary::init(['collection' => []]);
        $reflection = new \ReflectionClass(MockLibrary::getInstance());
        $validateCollectionsMethod = $reflection->getMethod('getFullTraceDetails');
        $validateCollectionsMethod->setAccessible(true);

        // invoke logic & test
        $validateCollectionsMethod->invoke(MockLibrary::getInstance(), new \stdClass(), 'fooBar');
    }
}