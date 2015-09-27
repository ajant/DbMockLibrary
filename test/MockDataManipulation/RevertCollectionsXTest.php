<?php
namespace DbMockLibrary\Test\MockDataManipulation;

use DbMockLibrary\MockDataManipulation;
use DbMockLibrary\Test\TestCase;

class RevertCollectionsXTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Collection \'fooBar\' does not exist');
        MockDataManipulation::initDataContainer(['collection' => []]);

        // invoke logic & test
        MockDataManipulation::getInstance()->revertCollections(['fooBar']);
    }
}