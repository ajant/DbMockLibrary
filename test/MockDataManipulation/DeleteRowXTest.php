<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class DeleteRowXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Collection \'fooBar\' does not exist');
        MockDataManipulation::initDataContainer(['collection' => ['id' => []]]);

        // invoke logic & test
        MockDataManipulation::getInstance()->deleteRow('fooBar', ['id']);
    }
}