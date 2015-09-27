<?php
namespace DbMockLibrary\Test\DataContainer;

use DbMockLibrary\DataContainer;
use DbMockLibrary\Test\TestCase;

class ValidateIdsTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        DataContainer::initDataContainer(['collection' => ['id' => []]]);

        // invoke logic & test
        $this->assertNull($this->invokeMethodByReflection(DataContainer::getInstance(), 'validateIds', ['collection', ['id']]));
    }
}