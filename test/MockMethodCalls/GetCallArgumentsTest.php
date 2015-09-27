<?php

use DbMockLibrary\MockMethodCalls;
use DbMockLibrary\Test\TestCase;

class GetCallArgumentsTest extends TestCase
{
    /**
     * Test function
     *
     * @return void
     */
    public function test_function()
    {
        // prepare
        MockMethodCalls::init();
        $callArguments = ['fooBar'];
        $this->setPropertyByReflection(MockMethodCalls::getInstance(), 'callArguments', $callArguments);

        // invoke logic & test
        $this->assertEquals($callArguments, MockMethodCalls::getInstance()->getCallArguments());
    }
}