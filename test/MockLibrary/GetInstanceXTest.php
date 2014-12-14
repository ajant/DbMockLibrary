<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class GetInstanceXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'MockLibrary object not initialized');

        // invoke logic & test
        MockLibrary::getInstance();
    }
}