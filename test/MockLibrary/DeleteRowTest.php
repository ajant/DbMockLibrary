<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class DeleteRowTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        $dataArray = ['collection' => ['id1' => [1],'id2' => [2]]];
        MockLibrary::init($dataArray);
        $reflection = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // invoke logic
        MockLibrary::getInstance()->deleteRow('collection', ['id1','id2']);

        // test
        $this->assertEquals(['collection' => []], $dataProperty->getValue(MockLibrary::getInstance()));
    }
}