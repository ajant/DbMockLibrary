<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class RevertCollectionsXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Collection \'fooBar\' does not exist');
        MockLibrary::init(['collection' => []]);

        // invoke logic & test
        MockLibrary::getInstance()->revertCollections(['fooBar']);
    }
}