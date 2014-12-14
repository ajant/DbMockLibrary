<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class ValidateCollectionsXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Collection \'fooBar\' does not exist');
        MockLibrary::init(['collection' => []]);
        $reflection = new \ReflectionClass(MockLibrary::getInstance());
        $validateCollectionsMethod = $reflection->getMethod('validateCollections');
        $validateCollectionsMethod->setAccessible(true);

        // invoke logic & test
        $validateCollectionsMethod->invoke(MockLibrary::getInstance(), ['collection']);
        $validateCollectionsMethod->invoke(MockLibrary::getInstance(), ['fooBar']);
    }
}