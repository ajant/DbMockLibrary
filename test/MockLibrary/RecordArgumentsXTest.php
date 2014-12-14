<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class RecordArgumentsXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Invalid method');
        MockLibrary::init(['collection' => []]);

        // invoke logic & test
        MockLibrary::getInstance()->recordArguments(new \stdClass(), 'fooBar', []);
    }
}