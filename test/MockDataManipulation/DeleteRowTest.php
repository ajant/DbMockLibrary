<?php
namespace DbMockLibrary\Test\MockDataManipulation;

use DbMockLibrary\MockDataManipulation;
use DbMockLibrary\Test\TestCase;

class DeleteRowTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        $dataArray = ['collection' => ['id1' => [1],'id2' => [2]]];
        MockDataManipulation::initDataContainer($dataArray);
        $reflection = new \ReflectionClass('\DbMockLibrary\MockDataManipulation');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // invoke logic
        MockDataManipulation::getInstance()->deleteRow('collection', ['id1','id2']);

        // test
        $this->assertEquals(['collection' => []], $dataProperty->getValue(MockDataManipulation::getInstance()));
    }
}