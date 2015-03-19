<?php
namespace Test\MockMethodCalls;

use \DbMockLibrary\MockMethodCalls;

class WasCalledXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Invalid method');
        MockMethodCalls::init(['collection' => ['id' => []]]);

        // invoke logic & test
        MockMethodCalls::getInstance()->wasCalledCount(new \stdClass(), 'fooBar');
    }
}