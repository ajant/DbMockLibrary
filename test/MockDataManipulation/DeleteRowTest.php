<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class DeleteRowTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        $dataArray = ['collection' => ['id1' => [1],'id2' => [2]]];
        MockDataManipulation::init($dataArray);
        $reflection = new \ReflectionClass('\DbMockLibrary\MockDataManipulation');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // invoke logic
        MockDataManipulation::getInstance()->deleteRow('collection', ['id1','id2']);

        // test
        $this->assertEquals(['collection' => []], $dataProperty->getValue(MockDataManipulation::getInstance()));
    }
}