<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class InitTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        $dataArray = [1];
        MockLibrary::init($dataArray);

        // prepare
        $reflection = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertInstanceOf('\DbMockLibrary\MockLibrary', $staticProperties['instance']);
        $this->assertEquals($dataArray, $staticProperties['initialData']);

        // prepare
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // test
        $this->assertEquals($dataArray, $dataProperty->getValue($staticProperties['instance']));
    }
}