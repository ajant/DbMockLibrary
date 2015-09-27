<?php
namespace DbMockLibrary\Test\DataContainer;

use DbMockLibrary\DataContainer;
use DbMockLibrary\Test\TestCase;

class InitDataContainerTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        $dataArray = [1];
        DataContainer::initDataContainer($dataArray);

        // prepare
        $reflection = new \ReflectionClass('\DbMockLibrary\DataContainer');
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertInstanceOf('\DbMockLibrary\DataContainer', $staticProperties['instance']);
        $this->assertEquals($dataArray, $staticProperties['initialData']);

        // prepare
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // test
        $this->assertEquals($dataArray, $dataProperty->getValue($staticProperties['instance']));
    }
}