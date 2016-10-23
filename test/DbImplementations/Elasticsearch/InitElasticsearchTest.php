<?php

namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use DbMockLibrary\DbImplementations\Elasticsearch;
use ReflectionClass;

class InitElasticsearchTest extends ElasticsearchTestCase
{
    /**
     * @return void
     */
    public function testInitialization()
    {
        // prepare
        $dataArray = [
            $this->testIndex => [
                [
                    'field1' => 'value1',
                    'field2' => 'value2',
                ],
            ],
        ];
        $indexTypes = [
            $this->testIndex => self::REGULAR_TYPE,
        ];

        // invoke logic
        Elasticsearch::initElasticsearch($this->client, $dataArray, [], $indexTypes);

        // prepare
        $reflection = new ReflectionClass(Elasticsearch::class);
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertInstanceOf(Elasticsearch::class, $staticProperties['instance']);
        $this->assertEquals($dataArray, $staticProperties['initialData']);

        // prepare
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);
        $indexTypesProperty = $reflection->getProperty('indexTypes');
        $indexTypesProperty->setAccessible(true);

        // test
        $this->assertEquals($dataArray, $dataProperty->getValue($staticProperties['instance']));
        $this->assertEquals($indexTypes, $indexTypesProperty->getValue($staticProperties['indexTypes']));
    }
}