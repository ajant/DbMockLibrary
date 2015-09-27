<?php
namespace DbMockLibrary\Test\DataContainer;

use DbMockLibrary\DataContainer;
use DbMockLibrary\Test\TestCase;

class ValidateCollectionsXTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Collection \'fooBar\' does not exist');
        DataContainer::initDataContainer(['collection' => []]);
        $reflection = new \ReflectionClass(DataContainer::getInstance());
        $validateCollectionsMethod = $reflection->getMethod('validateCollections');
        $validateCollectionsMethod->setAccessible(true);

        // invoke logic & test
        $validateCollectionsMethod->invoke(DataContainer::getInstance(), ['collection']);
        $validateCollectionsMethod->invoke(DataContainer::getInstance(), ['fooBar']);
    }
}