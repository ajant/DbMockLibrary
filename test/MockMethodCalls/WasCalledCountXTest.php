<?php
namespace DbMockLibrary\Test\MockMethodCalls;

use DbMockLibrary\MockMethodCalls;
use DbMockLibrary\Test\TestCase;

class WasCalledXTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Invalid method');
        MockMethodCalls::init();

        // invoke logic & test
        MockMethodCalls::getInstance()->wasCalledCount(new \stdClass(), 'fooBar');
    }
}