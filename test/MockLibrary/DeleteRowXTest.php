<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class DeleteRowXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Collection \'fooBar\' does not exist');
        MockLibrary::init(['collection' => ['id' => []]]);

        // invoke logic & test
        MockLibrary::getInstance()->deleteRow('fooBar', ['id']);
    }
}