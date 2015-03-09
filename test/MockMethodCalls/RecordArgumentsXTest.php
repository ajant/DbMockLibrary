<?php
namespace Test\MockMethodCalls;

use \DbMockLibrary\MockMethodCalls;

class RecordArgumentsXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Invalid method');
        MockMethodCalls::init(['collection' => []]);

        // invoke logic & test
        MockMethodCalls::getInstance()->recordArguments(new \stdClass(), 'fooBar', []);
    }
}