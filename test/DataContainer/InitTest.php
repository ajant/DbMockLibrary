<?php
namespace Test\DataContainer;

use \DbMockLibrary\DataContainer;

class InitTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        $dataArray = [1];
        DataContainer::init($dataArray);

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