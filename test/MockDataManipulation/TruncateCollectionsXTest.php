<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class TruncateCollectionsXTest extends \Test\TestCase
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
        MockDataManipulation::getInstance()->truncateCollections(['fooBar']);
    }
}