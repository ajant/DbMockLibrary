<?php
namespace Test\DataContainer;

use \DbMockLibrary\DataContainer;

class ResetDataTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $dataArray = [1];
        DataContainer::initDataContainer($dataArray);
        $reflection = new \ReflectionClass('\DbMockLibrary\DataContainer');
        $staticProperties = $reflection->getStaticProperties();
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // test
        $this->assertEquals($dataArray, $dataProperty->getValue($staticProperties['instance']));

        // prepare
        $dataProperty->setValue($staticProperties['instance'], [2]);

        // test
        $this->assertNotEquals($dataArray, $dataProperty->getValue($staticProperties['instance']));

        // invoke logic
        DataContainer::getInstance()->resetData();

        // test
        $this->assertEquals($dataArray, $dataProperty->getValue($staticProperties['instance']));
    }
}