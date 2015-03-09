<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class DropCollectionsXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Collection \'fooBar\' does not exist');
        MockDataManipulation::init(['collection' => []]);

        // invoke logic & test
        MockDataManipulation::getInstance()->dropCollections(['fooBar']);
    }
}