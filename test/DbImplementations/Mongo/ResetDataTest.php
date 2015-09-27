<?php

namespace DbMockLibrary\Test\DbImplementations\Mongo;

use DbMockLibrary\DbImplementations\Mongo;
use DbMockLibrary\Test\TestCase;

class ResetDataTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $dataArray = ['foo' => 1];
        Mongo::initMongo($dataArray, 'fooBar', []);
        $reflection = new \ReflectionClass('\DbMockLibrary\DbImplementations\Mongo');
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
        Mongo::getInstance()->resetData();

        // test
        $this->assertEquals($dataArray, $dataProperty->getValue($staticProperties['instance']));
    }
}