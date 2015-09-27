<?php
namespace DbMockLibrary\Test\MockMethodCalls;

use DbMockLibrary\MockMethodCalls;
use DbMockLibrary\Test\TestCase;

class RecordArgumentsXTest extends TestCase
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
        MockMethodCalls::getInstance()->recordArguments(new \stdClass(), 'fooBar', []);
    }
}