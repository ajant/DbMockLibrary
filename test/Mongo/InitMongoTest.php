<?php
namespace Test\Mongo;

use DbMockLibrary\DbImplementations\Mongo;

class InitTest extends \Test\TestCase
{
    public function tearDown()
    {
        Mongo::getInstance()->destroy();
    }

    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $dataArray = ['testCollection' => [1 => ['foo' => 1, 'id' => 1]]];

        // invoke logic
        Mongo::initMongo($dataArray, 'DbMockLibraryTest', []);

        // prepare
        $reflection = new \ReflectionClass('\DbMockLibrary\DbImplementations\Mongo');
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertInstanceOf('\DbMockLibrary\DbImplementations\Mongo', $staticProperties['instance']);
        $this->assertEquals($dataArray, $staticProperties['initialData']);

        // prepare
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // test
        $this->assertEquals($dataArray, $dataProperty->getValue($staticProperties['instance']));
    }
}