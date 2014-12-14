<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class WasCalledXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Invalid method');
        MockLibrary::init(['collection' => ['id' => []]]);

        // invoke logic & test
        MockLibrary::getInstance()->wasCalled(new \stdClass(), 'fooBar');
    }
}